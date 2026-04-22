<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include centralized database connection
require_once 'db_connection.php';

try {
    $pdo = getDBConnection();
    
    $clubname = isset($_GET['clubname']) ? $_GET['clubname'] : '';
    
    if (empty($clubname)) {
        echo json_encode(['error' => 'Club name is required']);
        exit;
    }
    
    // Fetch all members from the selected club
    $stmt = $pdo->prepare("SELECT id, membername, mobile, email, rotaryid FROM club_members WHERE clubname = ? ORDER BY membername");
    $stmt->execute([$clubname]);
    
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($members)) {
        echo json_encode(['error' => 'No members found for this club']);
    } else {
        echo json_encode($members);
    }
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>