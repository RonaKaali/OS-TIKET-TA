<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MfaSchema
{
    public static function ensureColumns(): array
    {
        $lines = [];
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            $statements = [
                'ALTER TABLE public.pengguna ADD COLUMN IF NOT EXISTS mfa_enabled BOOLEAN NOT NULL DEFAULT false',
                'ALTER TABLE public.pengguna ADD COLUMN IF NOT EXISTS mfa_secret TEXT NULL',
                'ALTER TABLE public.pengguna ADD COLUMN IF NOT EXISTS mfa_enabled_at TIMESTAMP NULL',
                'ALTER TABLE public.pengguna ADD COLUMN IF NOT EXISTS mfa_backup_codes JSONB NULL',
            ];

            foreach ($statements as $sql) {
                DB::unprepared($sql);
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

        // Reset koneksi pooler Supabase agar schema baru langsung dikenali
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

    /**
     * Simpan MFA langsung via query builder (hindari cache schema Eloquent/pooler).
     */
    public static function persistMfaForUser(int $userId, string $encryptedSecret): bool
    {
        if (!self::columnsExist()) {
            self::ensureColumns();
        }

        DB::disconnect();
        DB::reconnect();

        $now = now();

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            $updated = DB::update(
                'UPDATE public.pengguna SET mfa_secret = ?, mfa_enabled = true, mfa_enabled_at = ?, updated_at = ? WHERE id = ?',
                [$encryptedSecret, $now, $now, $userId]
            );
        } else {
            $updated = DB::table('pengguna')->where('id', $userId)->update([
                'mfa_secret' => $encryptedSecret,
                'mfa_enabled' => 1,
                'mfa_enabled_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $updated > 0;
    }

    public static function userHasMfa(int $userId): bool
    {
        $row = DB::table('pengguna')
            ->where('id', $userId)
            ->first(['mfa_enabled', 'mfa_secret']);

        if (!$row) {
            return false;
        }

        return filter_var($row->mfa_enabled ?? false, FILTER_VALIDATE_BOOLEAN)
            && !empty($row->mfa_secret);
    }

    public static function clearMfaForUser(int $userId): void
    {
        $now = now();

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::update(
                'UPDATE public.pengguna SET mfa_secret = NULL, mfa_enabled = false, mfa_enabled_at = NULL, mfa_backup_codes = NULL, updated_at = ? WHERE id = ?',
                [$now, $userId]
            );

            return;
        }

        DB::table('pengguna')->where('id', $userId)->update([
            'mfa_secret' => null,
            'mfa_enabled' => 0,
            'mfa_enabled_at' => null,
            'mfa_backup_codes' => null,
            'updated_at' => $now,
        ]);
    }

    public static function saveBackupCodes(int $userId, array $hashedCodes): void
    {
        if (!self::columnsExist()) {
            self::ensureColumns();
        }

        if (!Schema::hasColumn('pengguna', 'mfa_backup_codes')) {
            return;
        }

        DB::table('pengguna')->where('id', $userId)->update([
            'mfa_backup_codes' => json_encode(array_values($hashedCodes)),
            'updated_at' => now(),
        ]);
    }

    public static function getBackupCodes(int $userId): array
    {
        if (!Schema::hasColumn('pengguna', 'mfa_backup_codes')) {
            return [];
        }

        $raw = DB::table('pengguna')->where('id', $userId)->value('mfa_backup_codes');

        if (empty($raw)) {
            return [];
        }

        return is_array($raw) ? $raw : (json_decode($raw, true) ?: []);
    }
}
