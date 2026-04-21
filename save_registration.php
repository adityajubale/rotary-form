<?php
// error_reporting(0); // Don't show errors in output
// ini_set('display_errors', 0);

// और ये डालो (सिर्फ डीबगिंग के लिए)
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'rotary';

// Create upload directory if not exists
$upload_dir = __DIR__ . '/uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Function to generate unique registration ID
function generateRegistrationId($ticket_type) {
    $prefix = '';
    switch($ticket_type) {
        case 'single': $prefix = 'SNG'; break;
        case 'couple': $prefix = 'CPL'; break;
        case 'cohost_platinum': $prefix = 'PLT'; break;
        case 'cohost_gold': $prefix = 'GLD'; break;
        default: $prefix = 'REG';
    }
    return $prefix . date('Ymd') . substr(uniqid(), -6) . rand(100, 999);
}

// Function to sanitize filename
function sanitizeFilename($name) {
    $name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
    return substr($name, 0, 50);
}

// Function to save screenshot (optional)
function saveScreenshot($file, $person_name, $ticket_type) {
    global $upload_dir;
    
    // Screenshot is now optional - return null if not provided
    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error uploading file: ' . $file['error']);
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        throw new Exception('Invalid file type. Only JPG, PNG, PDF allowed.');
    }
    
    $max_size = 5 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        throw new Exception('File too large. Max 5MB allowed.');
    }
    
    $sanitized_name = sanitizeFilename($person_name);
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $timestamp = date('Ymd_His');
    $filename = $sanitized_name . '_' . $ticket_type . '_' . $timestamp . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }
    
    throw new Exception('Failed to save screenshot.');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

function sendConfirmationEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sednainfo5@gmail.com';   // 👈 अपना Gmail यहाँ लिखो
        $mail->Password   = 'mfzm afcu fwma latu'; // 👈 वो 16 अंकों वाला कोड यहाँ लिखो
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('sednainfo5@gmail.com', 'Rotary Party');
        $mail->addAddress($to);
        
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Connect to database
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ticket_type = $_POST['ticket_type'] ?? '';
        $uti_number = $_POST['uti_number'] ?? '';
        $email = $_POST['email'] ?? '';
        $mobile = $_POST['mobile'] ?? '';
        $amount_paid = $_POST['amount_paid'] ?? 0;
        
        if (!$ticket_type || !$uti_number || !$email || !$mobile) {
            throw new Exception('All required fields must be filled.');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address.');
        }
        
        $registration_id = generateRegistrationId($ticket_type);
        
        // Screenshot is now optional - process with or without it
        $screenshot_file = isset($_FILES['screenshot']) ? $_FILES['screenshot'] : null;
        
        // Process based on ticket type
        $person_name = '';
        $designation = '';
        $spouse_name = '';
        $full_name = '';
        $club_name = '';
        $food_preference = '';
        $alcohol = '';

        if ($ticket_type === 'single') {
            $full_name = $_POST['full_name'] ?? '';
            $designation = $_POST['designation'] ?? '';
            $role = $_POST['role'] ?? '';
            $club_id = $_POST['club_id'] ?? null;
            $club_name = $_POST['club_name'] ?? '';
            $food_preference = $_POST['food_preference'] ?? '';
            $alcohol = $_POST['alcohol'] ?? '';
            
            if (!$full_name) {
                throw new Exception('Full name is required.');
            }
            
            if (!$designation) {
                throw new Exception('Designation is required.');
            }
            
            if (!$role) {
                throw new Exception('Role is required.');
            }
            
            $person_name = $full_name;
            $screenshot_filename = saveScreenshot($screenshot_file, $person_name, $ticket_type);
            
            $sql = "INSERT INTO single_registrations (
                registration_id, full_name, uti_number, screenshot_filename, designation, role, email, mobile,
                club_id, club_name, food_preference, alcohol, amount_paid
            ) VALUES (
                :reg_id, :full_name, :uti, :ss_filename, :designation, :role, :email, :mobile,
                :club_id, :club_name, :food, :alcohol, :amount
            )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':reg_id' => $registration_id,
                ':full_name' => $full_name,
                ':uti' => $uti_number,
                ':ss_filename' => $screenshot_filename,
                ':designation' => $designation,
                ':role' => $role,
                ':email' => $email,
                ':mobile' => $mobile,
                ':club_id' => $club_id ?: null,
                ':club_name' => $club_name,
                ':food' => $food_preference,
                ':alcohol' => $alcohol,
                ':amount' => $amount_paid
            ]);
            
        } elseif ($ticket_type === 'couple') {
            $full_name = $_POST['full_name'] ?? '';
            $designation = $_POST['designation'] ?? '';
            $role = $_POST['role'] ?? '';
            $spouse_name = $_POST['spouse_name'] ?? '';
            $club_id = $_POST['club_id'] ?? null;
            $club_name = $_POST['club_name'] ?? '';
            $food_preference = $_POST['food_preference'] ?? '';
            $alcohol = $_POST['alcohol'] ?? '';
            
            if (!$full_name) {
                throw new Exception('Full name is required.');
            }
            
            if (!$designation) {
                throw new Exception('Designation is required.');
            }
            
            if (!$role) {
                throw new Exception('Role is required.');
            }
            
            if (!$spouse_name) {
                throw new Exception('Spouse name is required.');
            }
            
            $person_name = $full_name;
            $screenshot_filename = saveScreenshot($screenshot_file, $person_name, $ticket_type);
            
            $sql = "INSERT INTO couple_registrations (
                registration_id, full_name, uti_number, screenshot_filename, designation, role, spouse_name,
                email, mobile, club_id, club_name, food_preference, alcohol, amount_paid
            ) VALUES (
                :reg_id, :full_name, :uti, :ss_filename, :designation, :role, :spouse_name,
                :email, :mobile, :club_id, :club_name, :food, :alcohol, :amount
            )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':reg_id' => $registration_id,
                ':full_name' => $full_name,
                ':uti' => $uti_number,
                ':ss_filename' => $screenshot_filename,
                ':designation' => $designation,
                ':role' => $role,
                ':spouse_name' => $spouse_name,
                ':email' => $email,
                ':mobile' => $mobile,
                ':club_id' => $club_id ?: null,
                ':club_name' => $club_name,
                ':food' => $food_preference,
                ':alcohol' => $alcohol,
                ':amount' => $amount_paid
            ]);
            
        } elseif ($ticket_type === 'cohost_platinum') {
            $full_name = $_POST['full_name'] ?? '';
            
            if (!$full_name) {
                throw new Exception('Full name is required.');
            }
            
            $person_name = $full_name;
            $screenshot_filename = saveScreenshot($screenshot_file, $person_name, $ticket_type);
            
            $sql = "INSERT INTO cohost_platinum_registrations (
                registration_id, uti_number, screenshot_filename, full_name, email, mobile, amount_paid
            ) VALUES (
                :reg_id, :uti, :ss_filename, :full_name, :email, :mobile, :amount
            )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':reg_id' => $registration_id,
                ':uti' => $uti_number,
                ':ss_filename' => $screenshot_filename,
                ':full_name' => $full_name,
                ':email' => $email,
                ':mobile' => $mobile,
                ':amount' => $amount_paid
            ]);
            
        } elseif ($ticket_type === 'cohost_gold') {
            $full_name = $_POST['full_name'] ?? '';
            
            if (!$full_name) {
                throw new Exception('Full name is required.');
            }
            
            $person_name = $full_name;
            $screenshot_filename = saveScreenshot($screenshot_file, $person_name, $ticket_type);
            
            $sql = "INSERT INTO cohost_gold_registrations (
                registration_id, uti_number, screenshot_filename, full_name, email, mobile, amount_paid
            ) VALUES (
                :reg_id, :uti, :ss_filename, :full_name, :email, :mobile, :amount
            )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':reg_id' => $registration_id,
                ':uti' => $uti_number,
                ':ss_filename' => $screenshot_filename,
                ':full_name' => $full_name,
                ':email' => $email,
                ':mobile' => $mobile,
                ':amount' => $amount_paid
            ]);
            
        } else {
            throw new Exception('Invalid ticket type.');
        }
        
        $ticket_label = '';
        switch ($ticket_type) {
            case 'single': $ticket_label = 'Single Entry'; break;
            case 'couple': $ticket_label = 'Couple Entry'; break;
            case 'cohost_platinum': $ticket_label = 'Co Hosting Platinum'; break;
            case 'cohost_gold': $ticket_label = 'CO Hosting Gold'; break;
        }

        // Try to send email but don't fail if it doesn't work
        $email_sent = false;
        try {
            $email_subject = 'Rotary Party Registration Confirmation';
            $email_body = "Dear " . ($full_name ?: $person_name ?: 'Participant') . ",\n\n";
            $email_body .= "Thank you for registering for the Rotary Party event.\n\n";
            $email_body .= "Registration Details:\n";
            $email_body .= "Registration ID: {$registration_id}\n";
            $email_body .= "Ticket Type: {$ticket_label}\n";
            $email_body .= "Full Name: " . ($full_name ?: $person_name) . "\n";
            $email_body .= "UTI Number: {$uti_number}\n";
            $email_body .= "Amount Paid: ₹{$amount_paid}\n";
            if (!empty($designation)) {
                $email_body .= "Designation: {$designation}\n";
            }
            if (!empty($spouse_name)) {
                $email_body .= "Spouse Name: {$spouse_name}\n";
            }
            if (!empty($club_name)) {
                $email_body .= "Club: {$club_name}\n";
            }
            if (!empty($food_preference)) {
                $email_body .= "Food Preference: {$food_preference}\n";
            }
            if (!empty($alcohol)) {
                $email_body .= "Alcohol: {$alcohol}\n";
            }
            $email_body .= "\nPlease keep this email for your reference.\n\nRegards,\nRotary Registration Team";
            
            $email_sent = sendConfirmationEmail($email, $email_subject, $email_body);
        } catch (Exception $e) {
            $email_sent = false;
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Registration saved successfully!',
            'registration_id' => $registration_id,
            'email_sent' => $email_sent
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST allowed.'
    ]);
}
?>