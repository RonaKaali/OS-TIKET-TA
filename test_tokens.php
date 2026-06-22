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
    
    echo "=== PASSWORD RESET TOKENS ===\n";
    $stmt = $pdo->query("SELECT * FROM password_reset_tokens ORDER BY created_at DESC LIMIT 10");
    $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($tokens)) {
        echo "No tokens found in database!\n";
    } else {
        foreach ($tokens as $t) {
            echo "Email: " . $t['email'] . "\n";
            echo "Token (hashed): " . $t['token'] . "\n";
            echo "Created At: " . $t['created_at'] . "\n";
            echo "------------------------\n";
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
