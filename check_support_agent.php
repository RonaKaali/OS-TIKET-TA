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

    $email = 'support@csirt.kalselprov.go.id';

    // Cek user
    $stmt = $pdo->prepare("SELECT id, name, email FROM pengguna WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        echo "User SUDAH ADA:\n";
        echo "  ID   : " . $existing['id'] . "\n";
        echo "  Nama : " . $existing['name'] . "\n";
        echo "  Email: " . $existing['email'] . "\n";

        // Cek role
        $stmt2 = $pdo->prepare("
            SELECT r.name FROM model_has_roles mhr
            JOIN roles r ON r.id = mhr.role_id
            WHERE mhr.model_id = ? AND mhr.model_type = 'App\\\\Models\\\\User'
        ");
        $stmt2->execute([$existing['id']]);
        $roles = $stmt2->fetchAll(PDO::FETCH_COLUMN);
        echo "  Roles: " . implode(', ', $roles ?: ['(tidak ada)']) . "\n";
    } else {
        echo "User BELUM ADA. Membuat akun...\n";

        // Hash bcrypt manual (cost 12)
        $hashedPassword = password_hash('password', PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $pdo->prepare("
            INSERT INTO pengguna (name, email, password, email_verified_at, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW(), NOW())
            RETURNING id
        ");
        $stmt->execute(['Kepala Bidang CSIRT', $email, $hashedPassword]);
        $newId = $stmt->fetchColumn();

        echo "  User dibuat dengan ID: $newId\n";

        // Cek role 'Support Agent'
        $stmtRole = $pdo->prepare("SELECT id FROM roles WHERE name = 'Support Agent' LIMIT 1");
        $stmtRole->execute();
        $role = $stmtRole->fetch(PDO::FETCH_ASSOC);

        if ($role) {
            $stmtAssign = $pdo->prepare("
                INSERT INTO model_has_roles (role_id, model_type, model_id)
                VALUES (?, 'App\\\\Models\\\\User', ?)
                ON CONFLICT DO NOTHING
            ");
            $stmtAssign->execute([$role['id'], $newId]);
            echo "  Role 'Support Agent' berhasil di-assign!\n";
        } else {
            echo "  WARNING: Role 'Support Agent' tidak ditemukan di tabel roles!\n";
        }

        echo "\nAkun berhasil dibuat!\n";
        echo "  Email   : $email\n";
        echo "  Password: password\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
