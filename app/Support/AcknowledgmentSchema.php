<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AcknowledgmentSchema
{
    public static function ensureColumns(): array
    {
        $lines = [];
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            $sql = 'ALTER TABLE public.tiket ADD COLUMN IF NOT EXISTS acknowledged_at TIMESTAMP NULL';
            DB::unprepared($sql);
            $lines[] = 'OK: ' . $sql;
        } else {
            Schema::table('tiket', function ($table) {
                if (!Schema::hasColumn('tiket', 'acknowledged_at')) {
                    $table->timestamp('acknowledged_at')->nullable()->after('assigned_at');
                }
            });
            $lines[] = 'OK: kolom acknowledged_at dicek/ditambahkan ke tiket';
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
                      AND table_name = 'tiket'
                      AND column_name = 'acknowledged_at'
                ");

                return (int) ($result->total ?? 0) >= 1;
            }

            return Schema::hasColumn('tiket', 'acknowledged_at');
        } catch (\Throwable) {
            return false;
        }
    }
}
