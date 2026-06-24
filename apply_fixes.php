<?php
/**
 * Comprehensive PHP fixer untuk semua issues
 */

$files_to_fix = [
    // 1. TicketController.php - restore Agent 2
    [
        'file' => __DIR__ . '/app/Http/Controllers/Agent/TicketController.php',
        'find' => "\$q->where('roles.name', 'Agent 2');",
        'replace' => "\$q->whereIn('roles.name', RoleUi::ASSIGNABLE_AGENT_ROLES);",
    ],
];

$fixes_applied = 0;

foreach ($files_to_fix as $fix) {
    if (!file_exists($fix['file'])) {
        echo "❌ File not found: {$fix['file']}\n";
        continue;
    }

    $content = file_get_contents($fix['file']);
    
    if (strpos($content, $fix['find']) === false) {
        echo "⚠️  Pattern not found in {$fix['file']}\n";
        continue;
    }

    $new_content = str_replace($fix['find'], $fix['replace'], $content);
    
    if (file_put_contents($fix['file'], $new_content)) {
        echo "✅ Fixed: {$fix['file']}\n";
        $fixes_applied++;
    } else {
        echo "❌ Failed to write: {$fix['file']}\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Fixes applied: $fixes_applied / " . count($files_to_fix) . "\n";

// Now output what still needs to be done
echo "\n=== REMAINING TASKS ===\n";
echo "1. Fix SecurityEventLogService.php - aggressive device_registered filter (12 hour cooldown)\n";
echo "2. Update VerificationController.php - query untuk tampilkan tiket pending verification\n";
echo "3. Update field-agent.blade.php sidebar - add Verifikasi Surat Tugas menu\n";
echo "4. Update AuthenticatedSessionController.php - redirect after MFA ke admin dashboard\n";
echo "5. Restore portal dashboard route untuk user normal\n";
