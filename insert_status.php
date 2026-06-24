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
    
    // Check if it already exists
    $stmt = $pdo->prepare("SELECT id FROM status WHERE slug = ?");
    $stmt->execute(['menunggu_verifikasi_kepala_bidang']);
    $existing = $stmt->fetch();

    if (!$existing) {
        $stmt = $pdo->prepare("INSERT INTO status (name, slug, is_closed, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute(['Menunggu Verifikasi Kepala Bidang', 'menunggu_verifikasi_kepala_bidang', '0']);
        echo "Status inserted successfully.\n";
    } else {
        echo "Status already exists.\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
