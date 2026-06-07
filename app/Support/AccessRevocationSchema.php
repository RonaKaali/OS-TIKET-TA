<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AccessRevocationSchema
{
    public static function ensureColumns(): array
    {
        $lines = [];
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            $sql = 'ALTER TABLE public.pengguna ADD COLUMN IF NOT EXISTS access_revoked_at TIMESTAMP NULL';
            DB::unprepared($sql);
            $lines[] = 'OK: ' . $sql;
        } else {
            Schema::table('pengguna', function ($table) {
                if (!Schema::hasColumn('pengguna', 'access_revoked_at')) {
                    $table->timestamp('access_revoked_at')->nullable();
                }
            });
            $lines[] = 'OK: kolom access_revoked_at dicek/ditambahkan';
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
                      AND column_name = 'access_revoked_at'
                ");

                return (int) ($result->total ?? 0) >= 1;
            }

            return Schema::hasColumn('pengguna', 'access_revoked_at');
        } catch (\Throwable) {
            return false;
        }
    }
}
