<?php
// Database configuration
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

// Get all data from all tables
$singles = $pdo->query("SELECT * FROM single_registrations ORDER BY created_at DESC")->fetchAll();
$couples = $pdo->query("SELECT * FROM couple_registrations ORDER BY created_at DESC")->fetchAll();
$platinum = $pdo->query("SELECT * FROM cohost_platinum_registrations ORDER BY created_at DESC")->fetchAll();
$gold = $pdo->query("SELECT * FROM cohost_gold_registrations ORDER BY created_at DESC")->fetchAll();

// Get counts
$total_singles = count($singles);
$total_couples = count($couples);
$total_platinum = count($platinum);
$total_gold = count($gold);
$grand_total = $total_singles + $total_couples + $total_platinum + $total_gold;

// Calculate total revenue
$revenue_singles = array_sum(array_column($singles, 'amount_paid'));
$revenue_couples = array_sum(array_column($couples, 'amount_paid'));
$revenue_platinum = array_sum(array_column($platinum, 'amount_paid'));
$revenue_gold = array_sum(array_column($gold, 'amount_paid'));
$total_revenue = $revenue_singles + $revenue_couples + $revenue_platinum + $revenue_gold;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rotary Party - Complete Registration Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }
        
        .container {
            max-width: 1600px;
            margin: 0 auto;
        }
        
        /* Header Styles */
        .header {
            background: linear-gradient(135deg, #0b2b4b 0%, #1a4468 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .header-buttons {
            margin-top: 15px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 10px;
        }
        
        .btn-primary {
            background: #28a745;
            color: white;
        }
        
        .btn-primary:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #ffc107;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #e0a800;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .stat-card h3 {
            font-size: 32px;
            color: #0b2b4b;
            margin-bottom: 5px;
        }
        
        .stat-card p {
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }
        
        .stat-card.revenue {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .stat-card.revenue h3, .stat-card.revenue p {
            color: white;
        }
        
        /* Search Section */
        .search-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .search-box {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .search-box input {
            flex: 1;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: #0b2b4b;
        }
        
        .search-box select {
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            background: white;
            cursor: pointer;
        }
        
        .search-box button {
            padding: 12px 25px;
            background: #0b2b4b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        
        .search-box button:hover {
            background: #1a4468;
        }
        
        /* Section Styles */
        .section {
            background: white;
            margin-bottom: 30px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .section-header {
            background: #0b2b4b;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .section-header h2 {
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .badge {
            background: rgba(255,255,255,0.2);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .table-wrapper {
            overflow-x: auto;
            padding: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        
        th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
            position: sticky;
            top: 0;
        }
        
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .screenshot-link a {
            color: #0b2b4b;
            text-decoration: none;
            font-weight: 500;
        }
        
        .screenshot-link a:hover {
            text-decoration: underline;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 14px;
        }
        
        /* Export Section */
        .export-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 90%;
            max-height: 90%;
            overflow: auto;
            position: relative;
        }
        
        .modal-content img {
            max-width: 100%;
            height: auto;
        }
        
        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 30px;
            cursor: pointer;
            color: #333;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            th, td {
                font-size: 11px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>🎉 Rotary Party Registration Dashboard</h1>
        <p>Complete overview of all registrations</p>
        <div class="header-buttons">
            <a href="Payment.php" class="btn btn-primary">➕ New Registration</a>
            <button onclick="exportToExcel()" class="btn btn-secondary">📊 Export to Excel</button>
            <button onclick="window.print()" class="btn btn-secondary">🖨️ Print Report</button>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3><?php echo $total_singles; ?></h3>
            <p>🎫 Single Registrations</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_couples; ?></h3>
            <p>💑 Couple Registrations</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_platinum; ?></h3>
            <p>✨ Platinum Cohosts</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_gold; ?></h3>
            <p>🏅 Gold Cohosts</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $grand_total; ?></h3>
            <p>📊 Total Registrations</p>
        </div>
        <div class="stat-card revenue">
            <h3>₹<?php echo number_format($total_revenue, 2); ?></h3>
            <p>💰 Total Revenue</p>
        </div>
    </div>
    
    <!-- Search Section -->
    <div class="search-section">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search by name, email, mobile, UTI number..." onkeyup="searchAllTables()">
            <select id="typeFilter" onchange="filterByType()">
                <option value="all">All Types</option>
                <option value="single">Single Only</option>
                <option value="couple">Couple Only</option>
                <option value="platinum">Platinum Only</option>
                <option value="gold">Gold Only</option>
            </select>
            <button onclick="searchAllTables()">🔍 Search</button>
            <button onclick="clearSearch()">Clear</button>
        </div>
    </div>
    
    <!-- Single Registrations -->
    <div class="section" id="singleSection">
        <div class="section-header">
            <h2>🎫 Single Registrations <span class="badge"><?php echo $total_singles; ?> entries</span></h2>
        </div>
        <div class="table-wrapper">
            <?php if(count($singles) > 0): ?>
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
                <tr class="single-row">
                    <td><?php echo $s['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($s['registration_id']); ?></strong></td>
                    <td><?php echo htmlspecialchars($s['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($s['designation']); ?></td>
                    <td><?php echo htmlspecialchars($s['email']); ?></td>
                    <td><?php echo htmlspecialchars($s['mobile']); ?></td>
                    <td><?php echo htmlspecialchars($s['uti_number']); ?></td>
                    <td><?php echo htmlspecialchars($s['club_name']); ?></td>
                    <td><?php echo $s['food_preference']; ?></td>
                    <td><?php echo $s['alcohol']; ?></td>
                    <td><strong>₹<?php echo number_format($s['amount_paid'], 2); ?></strong></td>
                    <td class="screenshot-link">
                        <?php if($s['screenshot_filename']): ?>
                            <a href="javascript:void(0)" onclick="showImage('uploads/<?php echo urlencode($s['screenshot_filename']); ?>')">View</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('d-m-Y H:i', strtotime($s['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">No single registrations yet.</div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Couple Registrations -->
    <div class="section" id="coupleSection">
        <div class="section-header">
            <h2>💑 Couple Registrations <span class="badge"><?php echo $total_couples; ?> entries</span></h2>
        </div>
        <div class="table-wrapper">
            <?php if(count($couples) > 0): ?>
            <table id="coupleTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Reg ID</th>
                        <th>Primary Name</th>
                        <th>Designation</th>
                        <th>Spouse Name</th>
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
                <tr class="couple-row">
                    <td><?php echo $c['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($c['registration_id']); ?></strong></td>
                    <td><?php echo htmlspecialchars($c['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($c['designation']); ?></td>
                    <td><?php echo htmlspecialchars($c['spouse_name']); ?></td>
                    <td><?php echo htmlspecialchars($c['email']); ?></td>
                    <td><?php echo htmlspecialchars($c['mobile']); ?></td>
                    <td><?php echo htmlspecialchars($c['uti_number']); ?></td>
                    <td><?php echo htmlspecialchars($c['club_name']); ?></td>
                    <td><?php echo $c['food_preference']; ?></td>
                    <td><?php echo $c['alcohol']; ?></td>
                    <td><strong>₹<?php echo number_format($c['amount_paid'], 2); ?></strong></td>
                    <td class="screenshot-link">
                        <?php if($c['screenshot_filename']): ?>
                            <a href="javascript:void(0)" onclick="showImage('uploads/<?php echo urlencode($c['screenshot_filename']); ?>')">View</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('d-m-Y H:i', strtotime($c['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">No couple registrations yet.</div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Platinum Cohost Registrations -->
    <div class="section" id="platinumSection">
        <div class="section-header">
            <h2>✨ Co Hosting Platinum <span class="badge"><?php echo $total_platinum; ?> entries</span></h2>
        </div>
        <div class="table-wrapper">
            <?php if(count($platinum) > 0): ?>
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
                <tr class="platinum-row">
                    <td><?php echo $p['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($p['registration_id']); ?></strong></td>
                    <td><?php echo htmlspecialchars($p['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($p['email']); ?></td>
                    <td><?php echo htmlspecialchars($p['mobile']); ?></td>
                    <td><?php echo htmlspecialchars($p['uti_number']); ?></td>
                    <td><strong>₹<?php echo number_format($p['amount_paid'], 2); ?></strong></td>
                    <td class="screenshot-link">
                        <?php if($p['screenshot_filename']): ?>
                            <a href="javascript:void(0)" onclick="showImage('uploads/<?php echo urlencode($p['screenshot_filename']); ?>')">View</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('d-m-Y H:i', strtotime($p['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">No Platinum cohost registrations yet.</div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Gold Cohost Registrations -->
    <div class="section" id="goldSection">
        <div class="section-header">
            <h2>🏅 CO Hosting Gold <span class="badge"><?php echo $total_gold; ?> entries</span></h2>
        </div>
        <div class="table-wrapper">
            <?php if(count($gold) > 0): ?>
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
                <tr class="gold-row">
                    <td><?php echo $g['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($g['registration_id']); ?></strong></td>
                    <td><?php echo htmlspecialchars($g['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($g['email']); ?></td>
                    <td><?php echo htmlspecialchars($g['mobile']); ?></td>
                    <td><?php echo htmlspecialchars($g['uti_number']); ?></td>
                    <td><strong>₹<?php echo number_format($g['amount_paid'], 2); ?></strong></td>
                    <td class="screenshot-link">
                        <?php if($g['screenshot_filename']): ?>
                            <a href="javascript:void(0)" onclick="showImage('uploads/<?php echo urlencode($g['screenshot_filename']); ?>')">View</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('d-m-Y H:i', strtotime($g['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">No Gold cohost registrations yet.</div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Export Section -->
    <div class="export-section">
        <button onclick="exportToExcel()" class="btn btn-primary" style="margin-right: 10px;">📊 Export All to Excel</button>
        <button onclick="window.print()" class="btn btn-secondary">🖨️ Print Dashboard</button>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="modalImage" src="" alt="Screenshot">
    </div>
</div>

<script>
// Search functionality
function searchAllTables() {
    let searchText = document.getElementById('searchInput').value.toLowerCase();
    let typeFilter = document.getElementById('typeFilter').value;
    
    // Show/hide sections based on filter
    if (typeFilter === 'all' || typeFilter === 'single') {
        document.getElementById('singleSection').style.display = 'block';
    } else {
        document.getElementById('singleSection').style.display = 'none';
    }
    
    if (typeFilter === 'all' || typeFilter === 'couple') {
        document.getElementById('coupleSection').style.display = 'block';
    } else {
        document.getElementById('coupleSection').style.display = 'none';
    }
    
    if (typeFilter === 'all' || typeFilter === 'platinum') {
        document.getElementById('platinumSection').style.display = 'block';
    } else {
        document.getElementById('platinumSection').style.display = 'none';
    }
    
    if (typeFilter === 'all' || typeFilter === 'gold') {
        document.getElementById('goldSection').style.display = 'block';
    } else {
        document.getElementById('goldSection').style.display = 'none';
    }
    
    // Search in Single Table
    let singleTable = document.getElementById('singleTable');
    if (singleTable) {
        let rows = singleTable.getElementsByTagName('tbody')[0]?.getElementsByTagName('tr');
        if (rows) {
            for (let row of rows) {
                let text = row.innerText.toLowerCase();
                if (text.includes(searchText) || searchText === '') {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }
    }
    
    // Search in Couple Table
    let coupleTable = document.getElementById('coupleTable');
    if (coupleTable) {
        let rows = coupleTable.getElementsByTagName('tbody')[0]?.getElementsByTagName('tr');
        if (rows) {
            for (let row of rows) {
                let text = row.innerText.toLowerCase();
                if (text.includes(searchText) || searchText === '') {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }
    }
    
    // Search in Platinum Table
    let platinumTable = document.getElementById('platinumTable');
    if (platinumTable) {
        let rows = platinumTable.getElementsByTagName('tbody')[0]?.getElementsByTagName('tr');
        if (rows) {
            for (let row of rows) {
                let text = row.innerText.toLowerCase();
                if (text.includes(searchText) || searchText === '') {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }
    }
    
    // Search in Gold Table
    let goldTable = document.getElementById('goldTable');
    if (goldTable) {
        let rows = goldTable.getElementsByTagName('tbody')[0]?.getElementsByTagName('tr');
        if (rows) {
            for (let row of rows) {
                let text = row.innerText.toLowerCase();
                if (text.includes(searchText) || searchText === '') {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }
    }
}

function filterByType() {
    searchAllTables();
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('typeFilter').value = 'all';
    searchAllTables();
}

// Show image modal
function showImage(imagePath) {
    let modal = document.getElementById('imageModal');
    let modalImg = document.getElementById('modalImage');
    modal.style.display = 'flex';
    modalImg.src = imagePath;
}

function closeModal() {
    document.getElementById('imageModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    let modal = document.getElementById('imageModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Export to Excel
function exportToExcel() {
    let html = '<html><head><title>Rotary Party Registrations</title></head><body>';
    html += '<h1>Rotary Party - All Registrations</h1>';
    html += '<p>Generated on: ' + new Date().toLocaleString() + '</p>';
    
    // Add Single Registrations
    let singleTable = document.getElementById('singleTable');
    if (singleTable && singleTable.querySelector('tbody tr')) {
        html += '<h2>Single Registrations</h2>';
        html += '<table border="1" cellpadding="5" cellspacing="0">';
        html += singleTable.querySelector('thead').innerHTML;
        html += singleTable.querySelector('tbody').innerHTML;
        html += '</table><br><br>';
    }
    
    // Add Couple Registrations
    let coupleTable = document.getElementById('coupleTable');
    if (coupleTable && coupleTable.querySelector('tbody tr')) {
        html += '<h2>Couple Registrations</h2>';
        html += '<table border="1" cellpadding="5" cellspacing="0">';
        html += coupleTable.querySelector('thead').innerHTML;
        html += coupleTable.querySelector('tbody').innerHTML;
        html += '</table><br><br>';
    }
    
    // Add Platinum Registrations
    let platinumTable = document.getElementById('platinumTable');
    if (platinumTable && platinumTable.querySelector('tbody tr')) {
        html += '<h2>Co Hosting Platinum</h2>';
        html += '<table border="1" cellpadding="5" cellspacing="0">';
        html += platinumTable.querySelector('thead').innerHTML;
        html += platinumTable.querySelector('tbody').innerHTML;
        html += '</table><br><br>';
    }
    
    // Add Gold Registrations
    let goldTable = document.getElementById('goldTable');
    if (goldTable && goldTable.querySelector('tbody tr')) {
        html += '<h2>CO Hosting Gold</h2>';
        html += '<table border="1" cellpadding="5" cellspacing="0">';
        html += goldTable.querySelector('thead').innerHTML;
        html += goldTable.querySelector('tbody').innerHTML;
        html += '</table><br><br>';
    }
    
    html += '</body></html>';
    
    let blob = new Blob([html], { type: 'application/vnd.ms-excel' });
    let link = document.createElement('a');
    let url = URL.createObjectURL(blob);
    link.href = url;
    link.download = 'rotary_registrations_' + new Date().toISOString().slice(0,19) + '.xls';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

// Auto search on input
document.getElementById('searchInput').addEventListener('keyup', function() {
    searchAllTables();
});

// Initialize
searchAllTables();
</script>
</body>
</html>