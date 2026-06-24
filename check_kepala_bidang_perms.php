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

    echo "=== CEK PERMISSIONS KEPALA BIDANG ===\n\n";

    $userId = 7;
    $email = 'support@csirt.kalselprov.go.id';

    // Cek user
    $stmt = $pdo->prepare("SELECT id, name, email FROM pengguna WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "👤 User Found:\n";
        echo "  ID   : " . $user['id'] . "\n";
        echo "  Name : " . $user['name'] . "\n";
        echo "  Email: " . $user['email'] . "\n\n";
    }

    // Cek roles
    $stmtRoles = $pdo->query("
        SELECT r.id, r.name FROM model_has_roles mhr
        JOIN roles r ON r.id = mhr.role_id
        WHERE mhr.model_id = $userId AND mhr.model_type = 'App\\\\Models\\\\User'
    ");
    $roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);

    echo "👮 Roles:\n";
    if (empty($roles)) {
        echo "  ❌ Tidak ada roles!\n";
    } else {
        foreach ($roles as $role) {
            echo "  - " . $role['name'] . " (ID: {$role['id']})\n";
        }
    }

    echo "\n";

    // Cek permissions via roles
    $stmtPerms = $pdo->query("
        SELECT DISTINCT p.name 
        FROM model_has_roles mhr
        JOIN role_has_permissions rhp ON rhp.role_id = mhr.role_id
        JOIN permissions p ON p.id = rhp.permission_id
        WHERE mhr.model_id = $userId AND mhr.model_type = 'App\\\\Models\\\\User'
        ORDER BY p.name
    ");
    $perms = $stmtPerms->fetchAll(PDO::FETCH_COLUMN);

    echo "🔐 Permissions (via roles):\n";
    if (empty($perms)) {
        echo "  ❌ Tidak ada permissions!\n";
    } else {
        foreach ($perms as $perm) {
            echo "  - $perm\n";
        }
    }

    echo "\n";

    // Cek apakah punya permission 'admin.panel'
    $hasAdminPanel = in_array('admin.panel', $perms);
    echo ($hasAdminPanel ? "✅" : "❌") . " Has 'admin.panel' permission: " . ($hasAdminPanel ? "YES" : "NO") . "\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
