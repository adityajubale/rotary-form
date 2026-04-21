<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'rotary';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Using correct column name 'clubname' (not 'club_name')
    // Also using DISTINCT to show unique clubs only (since multiple members belong to same club)
    $stmt = $pdo->query("SELECT DISTINCT id, clubname as club_name FROM club_members ORDER BY clubname");
    
    $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If you want to show only unique club names (recommended for dropdown)
    // This prevents duplicate club entries
    $uniqueClubs = [];
    $seenClubs = [];
    foreach ($clubs as $club) {
        if (!in_array($club['club_name'], $seenClubs)) {
            $seenClubs[] = $club['club_name'];
            $uniqueClubs[] = $club;
        }
    }
    
    if (empty($uniqueClubs)) {
        echo json_encode(['error' => 'No clubs found in club_members table']);
    } else {
        echo json_encode($uniqueClubs);
    }
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>