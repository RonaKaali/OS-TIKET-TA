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

    // Update user Support Agent name ke "Kepala Bidang CSIRT"
    $stmt = $pdo->prepare("
        UPDATE pengguna 
        SET name = 'Kepala Bidang CSIRT'
        WHERE email = 'support@csirt.kalselprov.go.id'
        RETURNING id, name, email
    ");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo "✅ User berhasil diupdate:\n";
        echo "  ID   : " . $result['id'] . "\n";
        echo "  Nama : " . $result['name'] . "\n";
        echo "  Email: " . $result['email'] . "\n";
    } else {
        echo "❌ User tidak ditemukan atau gagal diupdate\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
