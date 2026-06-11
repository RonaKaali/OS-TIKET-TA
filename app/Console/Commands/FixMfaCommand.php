<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\MfaService;
use App\Support\MfaSchema;
use Illuminate\Console\Command;

class FixMfaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mfa:fix {email?} {--all} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose and fix MFA issues for users (corrupt secrets, invalid data)';

    protected MfaService $mfaService;

    public function __construct(MfaService $mfaService)
    {
        parent::__construct();
        $this->mfaService = $mfaService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $all = $this->option('all');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🔍 Checking MFA configuration...');

        // Check if MFA columns exist
        if (!MfaSchema::columnsExist()) {
            $this->error('❌ MFA columns do not exist in database!');
            $this->info('Run: php artisan migrate or visit /deploy-db');
            return 1;
        }

        $this->info('✅ MFA columns exist');

        // Get users to check
        if ($email) {
            $users = User::where('email', $email)->get();
            if ($users->isEmpty()) {
                $this->error("❌ User not found: {$email}");
                return 1;
            }
        } elseif ($all) {
            $users = User::all();
            $this->info("Checking ALL users ({$users->count()})");
        } else {
            $users = User::where('mfa_enabled', true)->get();
            $this->info("Checking users with MFA enabled ({$users->count()})");
        }

        $fixed = 0;
        $errors = 0;
        $skipped = 0;

        foreach ($users as $user) {
            $this->line('');
            $this->info("👤 User: {$user->email} (ID: {$user->id})");

            // Check MFA status
            $mfaEnabled = filter_var($user->mfa_enabled, FILTER_VALIDATE_BOOLEAN);
            $hasSecret = !empty($user->mfa_secret);

            $this->line("   MFA Enabled: " . ($mfaEnabled ? '✅ Yes' : '❌ No'));
            $this->line("   Has Secret: " . ($hasSecret ? '✅ Yes' : '❌ No'));

            if (!$mfaEnabled && !$hasSecret) {
                $this->line("   Status: ✅ OK (MFA not enabled)");
                $skipped++;
                continue;
            }

            // Check if secret is valid
            if ($hasSecret) {
                try {
                    $secret = decrypt($user->mfa_secret);
                    $this->line("   Secret Status: ✅ Valid (can decrypt)");
                    $this->line("   Secret Length: " . strlen($secret));
                    $skipped++;
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $this->error("   Secret Status: ❌ CORRUPT (DecryptException)");
                    $this->warn("   Error: {$e->getMessage()}");
                    
                    if (!$dryRun) {
                        $this->warn("   🔧 Fixing: Disabling MFA for this user...");
                        try {
                            MfaSchema::clearMfaForUser($user->id);
                            $user->refresh();
                            $this->info("   ✅ Fixed: MFA disabled");
                            $fixed++;
                        } catch (\Exception $clearError) {
                            $this->error("   ❌ Failed to fix: {$clearError->getMessage()}");
                            $errors++;
                        }
                    } else {
                        $this->info("   Would disable MFA (DRY RUN)");
                        $fixed++;
                    }
                } catch (\Exception $e) {
                    $this->error("   Secret Status: ❌ ERROR");
                    $this->warn("   Error: {$e->getMessage()}");
                    $errors++;
                }
            } else {
                // MFA enabled but no secret - inconsistent state
                $this->error("   Status: ❌ INCONSISTENT (enabled but no secret)");
                
                if (!$dryRun) {
                    $this->warn("   🔧 Fixing: Disabling MFA flag...");
                    try {
                        MfaSchema::clearMfaForUser($user->id);
                        $user->refresh();
                        $this->info("   ✅ Fixed: MFA disabled");
                        $fixed++;
                    } catch (\Exception $clearError) {
                        $this->error("   ❌ Failed to fix: {$clearError->getMessage()}");
                        $errors++;
                    }
                } else {
                    $this->info("   Would disable MFA (DRY RUN)");
                    $fixed++;
                }
            }
        }

        // Summary
        $this->line('');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 SUMMARY');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line("Total users checked: {$users->count()}");
        $this->line("✅ OK/Skipped: {$skipped}");
        $this->line("🔧 Fixed: {$fixed}");
        $this->line("❌ Errors: {$errors}");

        if ($dryRun && $fixed > 0) {
            $this->line('');
            $this->warn("⚠️  This was a DRY RUN. Run without --dry-run to apply fixes.");
        }

        return 0;
    }
}
