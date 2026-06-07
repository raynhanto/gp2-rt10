<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create galeri_foto child table
        Schema::create('galeri_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('galeri_id')->constrained('galeri')->cascadeOnDelete();
            $table->string('foto_url');
            $table->string('keterangan')->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });

        // 2. Add cover_url to galeri (album cover = first photo)
        Schema::table('galeri', function (Blueprint $table) {
            $table->string('cover_url')->nullable()->after('id');
        });

        // 3. Migrate existing foto_url → galeri_foto rows + set cover_url
        $now = now();
        DB::table('galeri')->get()->each(function ($g) use ($now) {
            if (!empty($g->foto_url)) {
                DB::table('galeri_foto')->insert([
                    'galeri_id'  => $g->id,
                    'foto_url'   => $g->foto_url,
                    'keterangan' => null,
                    'urutan'     => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                DB::table('galeri')->where('id', $g->id)->update(['cover_url' => $g->foto_url]);
            }
        });

        // 4. Drop old foto_url column
        Schema::table('galeri', function (Blueprint $table) {
            $table->dropColumn('foto_url');
        });
    }

    public function down(): void
    {
        Schema::table('galeri', function (Blueprint $table) {
            $table->string('foto_url')->nullable()->after('cover_url');
        });

        DB::table('galeri')->get()->each(function ($g) {
            $first = DB::table('galeri_foto')->where('galeri_id', $g->id)->orderBy('urutan')->value('foto_url');
            if ($first) {
                DB::table('galeri')->where('id', $g->id)->update(['foto_url' => $first]);
            }
        });

        Schema::table('galeri', function (Blueprint $table) {
            $table->dropColumn('cover_url');
        });

        Schema::dropIfExists('galeri_foto');
    }
};
