<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SeederManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SeederController extends Controller
{
    public function __construct(private readonly SeederManager $manager) {}

    public function index(): JsonResponse
    {
        $definitions = $this->manager->definitions();

        $runRecords = DB::table('seeder_runs')
            ->orderBy('run_at', 'desc')
            ->get()
            ->groupBy('seeder_key');

        $userCache = DB::table('users')->pluck('nama', 'id');

        $result = array_map(function (array $def) use ($runRecords, $userCache) {
            $runs = ($runRecords[$def['key']] ?? collect())
                ->map(fn($r) => [
                    'id'             => $r->id,
                    'status'         => $r->status,
                    'run_at'         => $r->run_at,
                    'run_by'         => $userCache[$r->run_by] ?? '—',
                    'rolled_back_at' => $r->rolled_back_at,
                    'rolled_back_by' => $r->rolled_back_by ? ($userCache[$r->rolled_back_by] ?? '—') : null,
                ])
                ->values()
                ->all();

            $lastApplied = collect($runs)->firstWhere('status', 'applied');

            $tableCounts = [];
            foreach ($def['tables'] as $table) {
                $tableCounts[$table] = (int) DB::table($table)->count();
            }
            $totalRows = array_sum($tableCounts);

            return [
                'key'          => $def['key'],
                'label'        => $def['label'],
                'description'  => $def['description'],
                'group'        => $def['group'],
                'depends_on'   => $def['depends_on'],
                'warning'      => $def['warning'],
                'tables'       => $def['tables'],
                'table_counts' => $tableCounts,
                'total_rows'   => $totalRows,
                'runs'         => $runs,
                'last_applied' => $lastApplied,
            ];
        }, $definitions);

        return response()->json(['data' => array_values($result)]);
    }

    public function run(string $key): JsonResponse
    {
        $def = $this->manager->find($key);
        if (!$def) {
            return response()->json(['error' => 'Seeder tidak ditemukan.'], 404);
        }

        // Enforce dependency order
        foreach ($def['depends_on'] as $dep) {
            $applied = DB::table('seeder_runs')
                ->where('seeder_key', $dep)
                ->where('status', 'applied')
                ->exists();

            if (!$applied) {
                $depDef  = $this->manager->find($dep);
                $depLabel = $depDef['label'] ?? $dep;
                return response()->json([
                    'error' => "Seeder \"{$depLabel}\" harus dijalankan terlebih dahulu.",
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            $snapshot  = $this->manager->snapshotBefore($def['tables']);
            $seeder    = app($def['class']);
            $seeder->run();
            $seededIds = $this->manager->captureInserted($snapshot);

            DB::table('seeder_runs')->insert([
                'seeder_key' => $key,
                'seeded_ids' => json_encode($seededIds),
                'run_by'     => auth()->id(),
                'run_at'     => now(),
                'status'     => 'applied',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json(['message' => "Seeder \"{$def['label']}\" berhasil dijalankan."]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error("SeederController::run({$key}) failed", [
                'error' => $e->getMessage(),
                'file'  => $e->getFile() . ':' . $e->getLine(),
            ]);
            return response()->json([
                'error' => 'Gagal menjalankan seeder: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function rollback(int $runId): JsonResponse
    {
        $run = DB::table('seeder_runs')->find($runId);
        if (!$run) {
            return response()->json(['error' => 'Run record tidak ditemukan.'], 404);
        }
        if ($run->status !== 'applied') {
            return response()->json(['error' => 'Seeder ini sudah di-rollback sebelumnya.'], 422);
        }

        // Block rollback if another applied seeder depends on this one
        foreach ($this->manager->definitions() as $other) {
            if (in_array($run->seeder_key, $other['depends_on'], true)) {
                $hasApplied = DB::table('seeder_runs')
                    ->where('seeder_key', $other['key'])
                    ->where('status', 'applied')
                    ->exists();

                if ($hasApplied) {
                    $otherLabel = $other['label'];
                    return response()->json([
                        'error' => "Seeder \"{$otherLabel}\" bergantung pada ini. Rollback seeder tersebut terlebih dahulu.",
                    ], 422);
                }
            }
        }

        $def   = $this->manager->find($run->seeder_key);
        $label = $def['label'] ?? $run->seeder_key;

        try {
            DB::beginTransaction();

            $seededIds = json_decode($run->seeded_ids ?? '{}', true) ?: [];
            $this->manager->rollbackIds($seededIds);

            DB::table('seeder_runs')->where('id', $runId)->update([
                'status'         => 'rolled_back',
                'rolled_back_by' => auth()->id(),
                'rolled_back_at' => now(),
                'updated_at'     => now(),
            ]);

            DB::commit();

            return response()->json(['message' => "Seeder \"{$label}\" berhasil di-rollback."]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error("SeederController::rollback({$runId}) failed", [
                'error' => $e->getMessage(),
                'file'  => $e->getFile() . ':' . $e->getLine(),
            ]);
            return response()->json(['error' => 'Gagal rollback: ' . $e->getMessage()], 500);
        }
    }
}
