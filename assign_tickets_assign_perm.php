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

    echo "=== ASSIGN 'tickets.assign' PERMISSION TO SUPPORT AGENT ROLE ===\n\n";

    // Get Support Agent role ID
    $stmtRole = $pdo->prepare("SELECT id FROM roles WHERE name = 'Support Agent'");
    $stmtRole->execute();
    $role = $stmtRole->fetch(PDO::FETCH_ASSOC);

    if (!$role) {
        echo "❌ Support Agent role tidak ditemukan!\n";
        exit;
    }

    // Get tickets.assign permission ID
    $stmtPerm = $pdo->prepare("SELECT id FROM permissions WHERE name = 'tickets.assign'");
    $stmtPerm->execute();
    $perm = $stmtPerm->fetch(PDO::FETCH_ASSOC);

    if (!$perm) {
        echo "❌ tickets.assign permission tidak ditemukan!\n";
        exit;
    }

    // Cek apakah sudah ada
    $stmtCheck = $pdo->prepare("
        SELECT * FROM role_has_permissions 
        WHERE role_id = ? AND permission_id = ?
    ");
    $stmtCheck->execute([$role['id'], $perm['id']]);
    $existing = $stmtCheck->fetch();

    if ($existing) {
        echo "✅ Permission sudah ter-assign sebelumnya.\n";
    } else {
        // Assign permission
        $stmtAssign = $pdo->prepare("
            INSERT INTO role_has_permissions (permission_id, role_id)
            VALUES (?, ?)
        ");
        $stmtAssign->execute([$perm['id'], $role['id']]);
        echo "✅ Permission 'tickets.assign' berhasil di-assign ke Support Agent role!\n";
    }

    // Verify all permissions
    echo "\nFinal Support Agent permissions:\n";
    
    $stmtVerify = $pdo->query("
        SELECT p.name 
        FROM role_has_permissions rhp
        JOIN permissions p ON p.id = rhp.permission_id
        WHERE rhp.role_id = " . $role['id'] . "
        ORDER BY p.name
    ");
    $allPerms = $stmtVerify->fetchAll(PDO::FETCH_COLUMN);

    foreach ($allPerms as $p) {
        echo "  ✓ $p\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
