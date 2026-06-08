<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExportService
{
    private function applyHeader(Spreadsheet $s, string $title): void
    {
        $sheet = $s->getActiveSheet();
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A3D2B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);
    }

    private function applyHeaderRow(Spreadsheet $s, int $row, array $cols): void
    {
        $sheet = $s->getActiveSheet();
        foreach (array_values($cols) as $i => $label) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}{$row}", $label);
        }
        $last = chr(64 + count($cols));
        $sheet->getStyle("A{$row}:{$last}{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '1A3D2B']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F4ED']],
        ]);
    }

    private function streamXlsx(Spreadsheet $s, string $filename): StreamedResponse
    {
        return new StreamedResponse(function () use ($s) {
            $writer = new Xlsx($s);
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function exportKas(?string $dari, ?string $sampai): StreamedResponse
    {
        $q = DB::table('kas as k')
            ->leftJoin('kategori_keuangan as kat', 'kat.id', '=', 'k.kategori_id')
            ->leftJoin('kampanye as ka', 'ka.id', '=', 'k.kampanye_id')
            ->leftJoin('users as u', 'u.id', '=', 'k.created_by')
            ->select('k.id', 'k.tanggal', 'k.created_at', 'k.jenis', 'kat.nama as kategori',
                     'k.keterangan', 'ka.judul', 'k.nominal', 'u.nama as oleh')
            ->whereNull('k.deleted_at')
            ->orderByDesc('k.tanggal')
            ->orderByDesc('k.created_at');

        if ($dari)   $q->where(DB::raw('COALESCE(k.tanggal, DATE(k.created_at))'), '>=', $dari);
        if ($sampai) $q->where(DB::raw('COALESCE(k.tanggal, DATE(k.created_at))'), '<=', $sampai);

        $rows = $q->get();

        $s     = new Spreadsheet();
        $sheet = $s->getActiveSheet()->setTitle('Kas');
        $this->applyHeader($s, 'Buku Kas RT 10 Golden Park 2');
        $this->applyHeaderRow($s, 2, ['ID', 'Tanggal', 'Jenis', 'Kategori', 'Keterangan', 'Kampanye', 'Nominal (Rp)', 'Oleh']);

        $totalMasuk = $totalKeluar = 0;
        foreach ($rows as $i => $r) {
            $rowNum = $i + 3;
            $tgl    = $r->tanggal ?? substr((string) $r->created_at, 0, 10);
            $sheet->fromArray([
                $r->id, $tgl, $r->jenis, $r->kategori ?? '', $r->keterangan,
                $r->judul ?? '', $r->nominal, $r->oleh ?? '',
            ], null, "A{$rowNum}");
            if ($r->jenis === 'masuk') $totalMasuk += $r->nominal;
            else $totalKeluar += $r->nominal;
        }

        $last = count($rows) + 4;
        $sheet->setCellValue("A{$last}", 'Total Masuk');
        $sheet->setCellValue("G{$last}", $totalMasuk);
        $sheet->setCellValue("A" . ($last + 1), 'Total Keluar');
        $sheet->setCellValue("G" . ($last + 1), $totalKeluar);
        $sheet->setCellValue("A" . ($last + 2), 'Saldo');
        $sheet->setCellValue("G" . ($last + 2), $totalMasuk - $totalKeluar);

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $this->streamXlsx($s, 'kas-rt10-' . now()->format('Ymd') . '.xlsx');
    }

    public function exportIuran(int $tahun): StreamedResponse
    {
        $bulanLabel = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                       'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $tagihan = DB::table('iuran_tagihan as t')
            ->join('iuran_periode as p', 'p.id', '=', 't.iuran_periode_id')
            ->join('unit_rumah as u', 'u.id', '=', 't.unit_rumah_id')
            ->select('u.blok', 'u.nomor', 'p.bulan', 't.status')
            ->where('p.tahun', $tahun)
            ->orderBy('u.blok')->orderBy('u.nomor')->orderBy('p.bulan')
            ->get();

        $matrix = [];
        foreach ($tagihan as $t) {
            $key           = $t->blok . $t->nomor;
            $matrix[$key]['blok']       = $t->blok;
            $matrix[$key]['nomor']      = $t->nomor;
            $matrix[$key][$t->bulan]    = $t->status;
        }

        $s     = new Spreadsheet();
        $sheet = $s->getActiveSheet()->setTitle("Iuran {$tahun}");
        $this->applyHeader($s, "Laporan Iuran Bulanan {$tahun} — RT 10 Golden Park 2");

        $headers = ['Blok', 'Nomor'];
        for ($b = 1; $b <= 12; $b++) $headers[] = $bulanLabel[$b];
        $headers[] = 'Total Lunas';
        $this->applyHeaderRow($s, 2, $headers);

        $statusColors = ['lunas' => 'C8F7D0', 'pending' => 'FFF3CD', 'belum' => 'FDECEA', 'dispensasi' => 'E9ECEF'];
        foreach (array_values($matrix) as $i => $unit) {
            $rowNum = $i + 3;
            $sheet->setCellValue("A{$rowNum}", $unit['blok']);
            $sheet->setCellValue("B{$rowNum}", $unit['nomor']);
            $lunas = 0;
            for ($b = 1; $b <= 12; $b++) {
                $col    = chr(66 + $b);
                $status = $unit[$b] ?? '';
                $sheet->setCellValue("{$col}{$rowNum}", $status ?: '—');
                if ($status === 'lunas') $lunas++;
                if ($status && isset($statusColors[$status])) {
                    $sheet->getStyle("{$col}{$rowNum}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($statusColors[$status]);
                }
            }
            $sheet->setCellValue("P{$rowNum}", $lunas);
        }

        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $this->streamXlsx($s, "iuran-{$tahun}-rt10.xlsx");
    }

    public function exportPengeluaran(?int $tahun, ?int $kategoriId): StreamedResponse
    {
        $q = DB::table('pengeluaran as p')
            ->leftJoin('kategori_keuangan as kat', 'kat.id', '=', 'p.kategori_id')
            ->leftJoin('kampanye as k', 'k.id', '=', 'p.kampanye_id')
            ->leftJoin('anggaran as a', 'a.id', '=', 'p.anggaran_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.created_by')
            ->select('p.id', 'p.tanggal', 'p.keterangan', 'kat.nama as kategori',
                     'k.judul', 'a.pos', 'p.nominal', 'u.nama as oleh')
            ->orderByDesc('p.tanggal');

        if ($tahun)     $q->whereYear('p.tanggal', $tahun);
        if ($kategoriId) $q->where('p.kategori_id', $kategoriId);

        $rows = $q->get();

        $s     = new Spreadsheet();
        $sheet = $s->getActiveSheet()->setTitle('Pengeluaran');
        $this->applyHeader($s, 'Laporan Pengeluaran RT 10 Golden Park 2');
        $this->applyHeaderRow($s, 2, ['ID', 'Tanggal', 'Keterangan', 'Kategori', 'Kampanye', 'Pos Anggaran', 'Nominal (Rp)', 'Oleh']);

        $total = 0;
        foreach ($rows as $i => $r) {
            $rowNum = $i + 3;
            $sheet->fromArray([
                $r->id, $r->tanggal, $r->keterangan, $r->kategori ?? '',
                $r->judul ?? '', $r->pos ?? '', $r->nominal, $r->oleh ?? '',
            ], null, "A{$rowNum}");
            $total += $r->nominal;
        }
        $last = count($rows) + 4;
        $sheet->setCellValue("A{$last}", 'Total');
        $sheet->setCellValue("G{$last}", $total);

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $this->streamXlsx($s, 'pengeluaran-rt10-' . now()->format('Ymd') . '.xlsx');
    }

    public function exportKependudukan(?string $blok): StreamedResponse
    {
        $q = DB::table('kepala_keluarga as kk')
            ->join('unit_rumah as u', 'u.id', '=', 'kk.unit_rumah_id')
            ->orderBy('u.blok')->orderBy('u.nomor');

        if ($blok) $q->where('u.blok', strtoupper($blok));

        $kepalaRows = $q->select(
            'kk.id', 'u.blok', 'u.nomor',
            'kk.nama', 'kk.nik', 'kk.no_kk', 'kk.no_wa', 'kk.status_tinggal'
        )->get();

        $anggota = DB::table('anggota_keluarga')
            ->whereIn('kepala_keluarga_id', $kepalaRows->pluck('id'))
            ->get()
            ->groupBy('kepala_keluarga_id');

        $s = new Spreadsheet();

        // Sheet 1: Daftar KK
        $sheet = $s->getActiveSheet()->setTitle('Daftar KK');
        $this->applyHeader($s, 'Laporan Kependudukan RT 10 Golden Park 2');
        $this->applyHeaderRow($s, 2, [
            'No', 'Blok', 'No.', 'Nama Kepala KK', 'NIK', 'No. KK', 'No. WA', 'Status Tinggal', 'Jml Anggota',
        ]);

        foreach ($kepalaRows as $i => $kk) {
            $row = $i + 3;
            $jumlahAnggota = isset($anggota[$kk->id]) ? $anggota[$kk->id]->count() : 0;
            $sheet->fromArray([
                $i + 1,
                $kk->blok,
                $kk->nomor,
                $kk->nama,
                $kk->nik ?? '',
                $kk->no_kk ?? '',
                $kk->no_wa ?? '',
                ucfirst($kk->status_tinggal ?? ''),
                $jumlahAnggota,
            ], null, "A{$row}");
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Sheet 2: Anggota Keluarga
        $s->createSheet()->setTitle('Anggota Keluarga');
        $s->setActiveSheetIndex(1);
        $sheet2 = $s->getActiveSheet();
        $this->applyHeaderRow($s, 1, [
            'Blok', 'No.', 'Nama KK', 'Nama Anggota', 'Hubungan', 'Jenis Kelamin', 'Tanggal Lahir',
        ]);

        $row2 = 2;
        foreach ($kepalaRows as $kk) {
            foreach ($anggota[$kk->id] ?? [] as $ag) {
                $sheet2->fromArray([
                    $kk->blok,
                    $kk->nomor,
                    $kk->nama,
                    $ag->nama,
                    $ag->hubungan ?? '',
                    $ag->jenis_kelamin === 'L' ? 'Laki-laki' : ($ag->jenis_kelamin === 'P' ? 'Perempuan' : ''),
                    $ag->tanggal_lahir ? substr($ag->tanggal_lahir, 0, 10) : '',
                ], null, "A{$row2}");
                $row2++;
            }
        }

        foreach (range('A', 'G') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        $s->setActiveSheetIndex(0);

        return $this->streamXlsx($s, 'kependudukan-rt10-' . now()->format('Ymd') . '.xlsx');
    }
}
