<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GpsSchema
{
    public static function ensureColumns(): array
    {
        $lines = [];
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            $statements = [
                'ALTER TABLE public.pengguna ADD COLUMN IF NOT EXISTS last_gps JSONB NULL',
                'ALTER TABLE public.pengguna ADD COLUMN IF NOT EXISTS last_gps_at TIMESTAMP NULL',
            ];

            foreach ($statements as $sql) {
                DB::unprepared($sql);
                $lines[] = 'OK: ' . $sql;
            }
        } else {
            Schema::table('pengguna', function ($table) {
                if (!Schema::hasColumn('pengguna', 'last_gps')) {
                    $table->json('last_gps')->nullable();
                }
                if (!Schema::hasColumn('pengguna', 'last_gps_at')) {
                    $table->timestamp('last_gps_at')->nullable();
                }
            });
            $lines[] = 'OK: kolom GPS dicek/ditambahkan via Schema builder';
        }

        DB::disconnect();
        DB::reconnect();

        return $lines;
    }

    public static function columnsExist(): bool
    {
        try {
            $driver = Schema::getConnection()->getDriverName();

            if ($driver === 'pgsql') {
                $result = DB::selectOne("
                    SELECT COUNT(*) AS total
                    FROM information_schema.columns
                    WHERE table_schema = 'public'
                      AND table_name = 'pengguna'
                      AND column_name IN ('last_gps', 'last_gps_at')
                ");

                return (int) ($result->total ?? 0) >= 2;
            }

            return Schema::hasColumn('pengguna', 'last_gps')
                && Schema::hasColumn('pengguna', 'last_gps_at');
        } catch (\Throwable) {
            return false;
        }
    }
}
