<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'rotary';
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get counts
$single_count = $pdo->query("SELECT COUNT(*) FROM single_registrations")->fetchColumn();
$couple_count = $pdo->query("SELECT COUNT(*) FROM couple_registrations")->fetchColumn();
$platinum_count = $pdo->query("SELECT COUNT(*) FROM cohost_platinum_registrations")->fetchColumn();
$gold_count = $pdo->query("SELECT COUNT(*) FROM cohost_gold_registrations")->fetchColumn();

// Get all data
$singles = $pdo->query("SELECT * FROM single_registrations ORDER BY created_at DESC")->fetchAll();
$couples = $pdo->query("SELECT * FROM couple_registrations ORDER BY created_at DESC")->fetchAll();
$platinum = $pdo->query("SELECT * FROM cohost_platinum_registrations ORDER BY created_at DESC")->fetchAll();
$gold = $pdo->query("SELECT * FROM cohost_gold_registrations ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rotary Party - All Registrations</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f0f0f0; }
        .container { max-width: 1400px; margin: 0 auto; }
        h1 { color: #0b2b4b; border-bottom: 3px solid #0b2b4b; padding-bottom: 10px; }
        .stats { display: flex; gap: 20px; margin: 20px 0; flex-wrap: wrap; }
        .stat-card { background: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-left: 4px solid #0b2b4b; }
        .stat-card h3 { margin: 0 0 5px 0; font-size: 28px; color: #0b2b4b; }
        .stat-card p { margin: 0; color: #666; }
        .section { background: white; margin: 25px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow-x: auto; }
        .section h2 { background: #0b2b4b; color: white; padding: 10px 15px; margin: -20px -20px 20px -20px; border-radius: 8px 8px 0 0; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; position: sticky; top: 0; }
        tr:hover { background: #f9f9f9; }
        .screenshot-link a { color: #0b2b4b; text-decoration: none; }
        .screenshot-link a:hover { text-decoration: underline; }
        .search-box {
            margin: 20px 0;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .search-box input {
            padding: 8px 12px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .search-box button {
            padding: 8px 16px;
            background: #0b2b4b;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }
        .search-box button:hover {
            background: #1a4468;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>📋 Rotary Party - Registration Dashboard</h1>
    <p><a href="payment.html" style="color: #0b2b4b; text-decoration: none; font-weight: bold;">➕ New Registration</a></p>
    
    <div class="stats">
        <div class="stat-card"><h3><?php echo $single_count; ?></h3><p>Single Registrations</p></div>
        <div class="stat-card"><h3><?php echo $couple_count; ?></h3><p>Couple Registrations</p></div>
        <div class="stat-card"><h3><?php echo $platinum_count; ?></h3><p>Co Hosting Platinum</p></div>
        <div class="stat-card"><h3><?php echo $gold_count; ?></h3><p>CO Hosting Gold</p></div>
        <div class="stat-card"><h3><?php echo $single_count + $couple_count + $platinum_count + $gold_count; ?></h3><p>Total Registrations</p></div>
    </div>
    
    <!-- Search Box -->
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search by Name, Email, Mobile or UTI..." onkeyup="searchRegistrations()">
        <button onclick="searchRegistrations()">🔍 Search</button>
        <button onclick="clearSearch()">Clear</button>
    </div>
    
    <!-- Single Registrations -->
    <div class="section">
        <h2>🎫 Single Registrations</h2>
        <?php if(count($singles) > 0): ?>
        <div style="overflow-x: auto;">
        <table id="singleTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Reg ID</th>
                    <th>Full Name</th>
                    <th>Designation</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>UTI Number</th>
                    <th>Club</th>
                    <th>Food</th>
                    <th>Alcohol</th>
                    <th>Amount</th>
                    <th>Screenshot</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($singles as $s): ?>
            <tr>
                <td><?php echo $s['id']; ?></td>
                <td><?php echo htmlspecialchars($s['registration_id']); ?></td>
                <td><?php echo htmlspecialchars($s['full_name'] ?? $s['designation']); ?></td>
                <td><?php echo htmlspecialchars($s['designation']); ?></td>
                <td><?php echo htmlspecialchars($s['email']); ?></td>
                <td><?php echo htmlspecialchars($s['mobile']); ?></td>
                <td><?php echo htmlspecialchars($s['uti_number']); ?></td>
                <td><?php echo htmlspecialchars($s['club_name']); ?></td>
                <td><?php echo $s['food_preference']; ?></td>
                <td><?php echo $s['alcohol']; ?></td>
                <td>₹<?php echo $s['amount_paid']; ?></td>
                <td class="screenshot-link"><?php if($s['screenshot_filename']): ?><a href="uploads/<?php echo urlencode($s['screenshot_filename']); ?>" target="_blank">View Screenshot</a><?php else: ?>N/A<?php endif; ?></td>
                <td><?php echo date('d-m-Y H:i', strtotime($s['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php else: ?>
        <p>No single registrations yet.</p>
        <?php endif; ?>
    </div>
    
    <!-- Couple Registrations -->
    <div class="section">
        <h2>💑 Couple Registrations</h2>
        <?php if(count($couples) > 0): ?>
        <div style="overflow-x: auto;">
        <table id="coupleTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Reg ID</th>
                    <th>Designation</th>
                    <th>Spouse</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>UTI Number</th>
                    <th>Club</th>
                    <th>Food</th>
                    <th>Alcohol</th>
                    <th>Amount</th>
                    <th>Screenshot</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($couples as $c): ?>
            <tr>
                <td><?php echo $c['id']; ?></td>
                <td><?php echo htmlspecialchars($c['registration_id']); ?></td>
                <td><?php echo htmlspecialchars($c['designation']); ?></td>
                <td><?php echo htmlspecialchars($c['spouse_name']); ?></td>
                <td><?php echo htmlspecialchars($c['email']); ?></td>
                <td><?php echo htmlspecialchars($c['mobile']); ?></td>
                <td><?php echo htmlspecialchars($c['uti_number']); ?></td>
                <td><?php echo htmlspecialchars($c['club_name']); ?></td>
                <td><?php echo $c['food_preference']; ?></td>
                <td><?php echo $c['alcohol']; ?></td>
                <td>₹<?php echo $c['amount_paid']; ?></td>
                <td class="screenshot-link"><?php if($c['screenshot_filename']): ?><a href="uploads/<?php echo urlencode($c['screenshot_filename']); ?>" target="_blank">View Screenshot</a><?php else: ?>N/A<?php endif; ?></td>
                <td><?php echo date('d-m-Y H:i', strtotime($c['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php else: ?>
        <p>No couple registrations yet.</p>
        <?php endif; ?>
    </div>
    
    <!-- Co Hosting Platinum -->
    <div class="section">
        <h2>✨ Co Hosting Platinum</h2>
        <?php if(count($platinum) > 0): ?>
        <div style="overflow-x: auto;">
        <table id="platinumTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Reg ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>UTI Number</th>
                    <th>Amount</th>
                    <th>Screenshot</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($platinum as $p): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><?php echo htmlspecialchars($p['registration_id']); ?></td>
                <td><?php echo htmlspecialchars($p['full_name']); ?></td>
                <td><?php echo htmlspecialchars($p['email']); ?></td>
                <td><?php echo htmlspecialchars($p['mobile']); ?></td>
                <td><?php echo htmlspecialchars($p['uti_number']); ?></td>
                <td>₹<?php echo $p['amount_paid']; ?></td>
                <td class="screenshot-link"><?php if($p['screenshot_filename']): ?><a href="uploads/<?php echo urlencode($p['screenshot_filename']); ?>" target="_blank">View Screenshot</a><?php else: ?>N/A<?php endif; ?></td>
                <td><?php echo date('d-m-Y H:i', strtotime($p['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php else: ?>
        <p>No Co Hosting Platinum registrations yet.</p>
        <?php endif; ?>
    </div>
    
    <!-- CO Hosting Gold -->
    <div class="section">
        <h2>🏅 CO Hosting Gold</h2>
        <?php if(count($gold) > 0): ?>
        <div style="overflow-x: auto;">
        <table id="goldTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Reg ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>UTI Number</th>
                    <th>Amount</th>
                    <th>Screenshot</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($gold as $g): ?>
            <tr>
                <td><?php echo $g['id']; ?></td>
                <td><?php echo htmlspecialchars($g['registration_id']); ?></td>
                <td><?php echo htmlspecialchars($g['full_name']); ?></td>
                <td><?php echo htmlspecialchars($g['email']); ?></td>
                <td><?php echo htmlspecialchars($g['mobile']); ?></td>
                <td><?php echo htmlspecialchars($g['uti_number']); ?></td>
                <td>₹<?php echo $g['amount_paid']; ?></td>
                <td class="screenshot-link"><?php if($g['screenshot_filename']): ?><a href="uploads/<?php echo urlencode($g['screenshot_filename']); ?>" target="_blank">View Screenshot</a><?php else: ?>N/A<?php endif; ?></td>
                <td><?php echo date('d-m-Y H:i', strtotime($g['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php else: ?>
        <p>No CO Hosting Gold registrations yet.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function searchRegistrations() {
    let searchText = document.getElementById('searchInput').value.toLowerCase();
    
    // Search in Single Registrations
    let singleTable = document.getElementById('singleTable');
    if (singleTable) {
        let rows = singleTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        for (let row of rows) {
            let fullName = row.cells[2]?.innerText.toLowerCase() || '';
            let email = row.cells[4]?.innerText.toLowerCase() || '';
            let mobile = row.cells[5]?.innerText.toLowerCase() || '';
            let uti = row.cells[6]?.innerText.toLowerCase() || '';
            
            if (fullName.includes(searchText) || email.includes(searchText) || mobile.includes(searchText) || uti.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }
    
    // Search in Couple Registrations
    let coupleTable = document.getElementById('coupleTable');
    if (coupleTable) {
        let rows = coupleTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        for (let row of rows) {
            let designation = row.cells[2]?.innerText.toLowerCase() || '';
            let spouse = row.cells[3]?.innerText.toLowerCase() || '';
            let email = row.cells[4]?.innerText.toLowerCase() || '';
            let mobile = row.cells[5]?.innerText.toLowerCase() || '';
            let uti = row.cells[6]?.innerText.toLowerCase() || '';
            
            if (designation.includes(searchText) || spouse.includes(searchText) || email.includes(searchText) || mobile.includes(searchText) || uti.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }
    
    // Search in Platinum Registrations
    let platinumTable = document.getElementById('platinumTable');
    if (platinumTable) {
        let rows = platinumTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        for (let row of rows) {
            let fullName = row.cells[2]?.innerText.toLowerCase() || '';
            let email = row.cells[3]?.innerText.toLowerCase() || '';
            let mobile = row.cells[4]?.innerText.toLowerCase() || '';
            let uti = row.cells[5]?.innerText.toLowerCase() || '';
            
            if (fullName.includes(searchText) || email.includes(searchText) || mobile.includes(searchText) || uti.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }
    
    // Search in Gold Registrations
    let goldTable = document.getElementById('goldTable');
    if (goldTable) {
        let rows = goldTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        for (let row of rows) {
            let fullName = row.cells[2]?.innerText.toLowerCase() || '';
            let email = row.cells[3]?.innerText.toLowerCase() || '';
            let mobile = row.cells[4]?.innerText.toLowerCase() || '';
            let uti = row.cells[5]?.innerText.toLowerCase() || '';
            
            if (fullName.includes(searchText) || email.includes(searchText) || mobile.includes(searchText) || uti.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    searchRegistrations();
}

// Optional: Add export to Excel functionality
function exportToExcel() {
    window.location.href = 'export_registrations.php';
}
</script>

</body>
</html>