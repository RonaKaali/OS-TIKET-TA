<?php
$host = "aws-1-ap-southeast-1.pooler.supabase.com";
$port = "6543";
$dbname = "postgres";
$user = "postgres.buxpzivjpkafyllprrfi";
$password = "OsTiket_Sidang2024!";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => true,
    ]);

    $userId = 7;
    $email = 'support@csirt.kalselprov.go.id';

    // Tampilkan semua role yang ada
    $stmtAllRoles = $pdo->query("SELECT id, name FROM roles ORDER BY id");
    $allRoles = $stmtAllRoles->fetchAll(PDO::FETCH_ASSOC);
    echo "=== SEMUA ROLE DI DATABASE ===\n";
    foreach ($allRoles as $r) {
        echo "  ID {$r['id']}: {$r['name']}\n";
    }
    echo "\n";

    // Cek role 'Support Agent'
    $stmtRole = $pdo->prepare("SELECT id FROM roles WHERE name = 'Support Agent' LIMIT 1");
    $stmtRole->execute();
    $role = $stmtRole->fetch(PDO::FETCH_ASSOC);

    if ($role) {
        // Cek apakah sudah ada
        $stmtCheck = $pdo->prepare("SELECT * FROM model_has_roles WHERE role_id = ? AND model_type = 'App\\\\Models\\\\User' AND model_id = ?");
        $stmtCheck->execute([$role['id'], $userId]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            echo "Role sudah ter-assign sebelumnya.\n";
        } else {
            $stmtAssign = $pdo->prepare("
                INSERT INTO model_has_roles (role_id, model_type, model_id)
                VALUES (?, 'App\\\\Models\\\\User', ?)
            ");
            $stmtAssign->execute([$role['id'], $userId]);
            echo "✅ Role 'Support Agent' berhasil di-assign ke user ID $userId ($email)!\n";
        }
    } else {
        echo "❌ Role 'Support Agent' TIDAK DITEMUKAN di database.\n";
    }

    // Konfirmasi
    $stmtConfirm = $pdo->prepare("
        SELECT r.name FROM model_has_roles mhr
        JOIN roles r ON r.id = mhr.role_id
        WHERE mhr.model_id = ? AND mhr.model_type = 'App\\\\Models\\\\User'
    ");
    $stmtConfirm->execute([$userId]);
    $roles = $stmtConfirm->fetchAll(PDO::FETCH_COLUMN);
    echo "\nRole user sekarang: " . implode(', ', $roles ?: ['(tidak ada)']) . "\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
