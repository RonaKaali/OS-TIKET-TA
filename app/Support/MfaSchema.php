<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MfaSchema
{
    /**
     * Pastikan kolom MFA ada di tabel pengguna (PostgreSQL / MySQL).
     */
    public static function ensureColumns(): array
    {
        $lines = [];
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            $statements = [
                'ALTER TABLE pengguna ADD COLUMN IF NOT EXISTS mfa_enabled BOOLEAN NOT NULL DEFAULT false',
                'ALTER TABLE pengguna ADD COLUMN IF NOT EXISTS mfa_secret TEXT NULL',
                'ALTER TABLE pengguna ADD COLUMN IF NOT EXISTS mfa_enabled_at TIMESTAMP NULL',
                'ALTER TABLE pengguna ADD COLUMN IF NOT EXISTS mfa_backup_codes JSONB NULL',
            ];

            foreach ($statements as $sql) {
                DB::statement($sql);
                $lines[] = 'OK: ' . $sql;
            }
        } else {
            Schema::table('pengguna', function ($table) {
                if (!Schema::hasColumn('pengguna', 'mfa_enabled')) {
                    $table->boolean('mfa_enabled')->default(false);
                }
                if (!Schema::hasColumn('pengguna', 'mfa_secret')) {
                    $table->text('mfa_secret')->nullable();
                }
                if (!Schema::hasColumn('pengguna', 'mfa_enabled_at')) {
                    $table->timestamp('mfa_enabled_at')->nullable();
                }
                if (!Schema::hasColumn('pengguna', 'mfa_backup_codes')) {
                    $table->json('mfa_backup_codes')->nullable();
                }
            });
            $lines[] = 'OK: kolom MFA dicek/ditambahkan via Schema builder';
        }

        return $lines;
    }

    /**
     * Cek kolom MFA benar-benar ada (query langsung ke DB, bukan cache schema).
     */
    public static function columnsExist(): bool
    {
        try {
            $driver = Schema::getConnection()->getDriverName();

            if ($driver === 'pgsql') {
                $result = DB::selectOne("
                    SELECT COUNT(*) AS total
                    FROM information_schema.columns
                    WHERE table_schema = current_schema()
                      AND table_name = 'pengguna'
                      AND column_name IN ('mfa_enabled', 'mfa_secret', 'mfa_enabled_at')
                ");

                return (int) ($result->total ?? 0) >= 3;
            }

            return Schema::hasColumn('pengguna', 'mfa_enabled')
                && Schema::hasColumn('pengguna', 'mfa_secret')
                && Schema::hasColumn('pengguna', 'mfa_enabled_at');
        } catch (\Throwable) {
            return false;
        }
    }
}
