<?php
// Test script to check database and table
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'rotary';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if table exists
    $result = $pdo->query("SHOW TABLES LIKE 'cohost_silver_registrations'");
    if ($result->rowCount() > 0) {
        echo "Table cohost_silver_registrations exists.\n";
    } else {
        echo "Table cohost_silver_registrations does not exist.\n";
    }
    
    // Test insert (commented out)
    // $stmt = $pdo->prepare("INSERT INTO cohost_silver_registrations (registration_id, uti_number, full_name, email, mobile, amount_paid) VALUES (?, ?, ?, ?, ?, ?)");
    // $stmt->execute(['TEST123', 'UTI123', 'Test Club', 'test@example.com', '1234567890', 2500]);
    // echo "Test insert successful.\n";
    
} catch (PDOException $e) {
    echo 'Database error: ' . $e->getMessage() . "\n";
}
?>