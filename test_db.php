<?php
header('Content-Type: text/html');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db_host = 'localhost';
$db_user = 'sednaris_bhavi';
$db_pass = '!dKyAd9..{Ux';
$db_name = 'sednaris_bhavi';


echo "<h2>Database Connection Test</h2>";

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>✓ Connected to database 'rotary' successfully!</p>";
    
    // Check if club_members table exists
    $result = $pdo->query("SHOW TABLES LIKE 'club_members'");
    if ($result->rowCount() > 0) {
        echo "<p style='color:green'>✓ Table 'club_members' exists</p>";
        
        // Show table structure
        echo "<h3>Table Structure:</h3>";
        $columns = $pdo->query("DESCRIBE club_members");
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($col = $columns->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show data in the table
        echo "<h3>Data in club_members:</h3>";
        $data = $pdo->query("SELECT * FROM club_members");
        if ($data->rowCount() > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr>";
            for ($i = 0; $i < $data->columnCount(); $i++) {
                $col = $data->getColumnMeta($i);
                echo "<th>{$col['name']}</th>";
            }
            echo "</tr>";
            while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color:orange'>⚠ No data found in club_members table</p>";
        }
    } else {
        echo "<p style='color:red'>✗ Table 'club_members' does NOT exist!</p>";
        
        // Show all tables in database
        echo "<h3>Available tables in database:</h3>";
        $tables = $pdo->query("SHOW TABLES");
        echo "<ul>";
        while ($table = $tables->fetch(PDO::FETCH_NUM)) {
            echo "<li>" . $table[0] . "</li>";
        }
        echo "</ul>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>