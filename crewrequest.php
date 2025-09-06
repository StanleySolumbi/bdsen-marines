<?php
// Hide PHP errors from users (log them instead in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company    = $_POST['company'] ?? '';
    $email      = $_POST['email'] ?? '';
    $jobType    = $_POST['jobType'] ?? '';
    $vesselType = $_POST['vesselType'] ?? '';
    $crew       = $_POST['crew'] ?? '';

    $mail = new PHPMailer(true);

    try {
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host       = 'bdsen-marines.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'crewinfo@bdsen-marines.com';
        $mail->Password   = 'Stanley_08069795682'; // ⚠️ Use env/config file in production
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Sender & Recipient
        $mail->setFrom('crewinfo@bdsen-marines.com', 'BDS Energy & Marine - Crew Request');
        $mail->addAddress('crewinfo@bdsen-marines.com');
        if (!empty($email)) {
            $mail->addReplyTo($email, $company);
        }

        // ✅ File Upload Validation (CV Template)
        if (isset($_FILES['template']) && $_FILES['template']['error'] == UPLOAD_ERR_OK) {
            $allowedExtensions = ['pdf', 'doc', 'docx', 'rtf', 'odt', 'xls', 'xlsx'];
            $fileName = $_FILES['template']['name'];
            $fileTmp  = $_FILES['template']['tmp_name'];
            $fileSize = $_FILES['template']['size'];
            $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($fileExt, $allowedExtensions)) {
                throw new Exception("Invalid file type. Only PDF, Word, Excel, RTF, and ODT files are allowed.");
            }

            if ($fileSize > 2 * 1024 * 1024) { // 2MB max
                throw new Exception("File too large. Maximum size is 2MB.");
            }

            $mail->addAttachment($fileTmp, $fileName);
        }

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = "New Crew Request from {$company}";
        $mail->Body    = "
            <h3>New Crew Request Received</h3>
            <p><b>Company:</b> {$company}</p>
            <p><b>Email:</b> {$email}</p>
            <p><b>Job Type:</b> {$jobType}</p>
            <p><b>Vessel Type:</b> {$vesselType}</p>
            <p><b>Crew Details:</b><br>{$crew}</p>
            <p><i>Attachment: CV template (if provided) is attached.</i></p>
        ";

        $mail->SMTPDebug = 0; // disable debug logs for production

        $mail->send();

        // ✅ Success Page
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
          <meta charset='UTF-8'>
          <meta name='viewport' content='width=device-width, initial-scale=1.0'>
          <title>Crew Request Submitted</title>
          <style>
            body {font-family: Arial, sans-serif; background:#f7f9fc; display:flex; justify-content:center; align-items:center; height:100vh; margin:0;}
            .box {background:#fff; padding:2rem; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); text-align:center; max-width:450px;}
            .box h2 {color:#2b7a2b;}
            .btn {display:inline-block; margin-top:1.5rem; padding:0.75rem 1.5rem; background:#2b7a2b; color:#fff; text-decoration:none; border-radius:8px;}
            .btn:hover {background:#256b25;}
          </style>
        </head>
        <body>
          <div class='box'>
            <div style='font-size:3rem;'>✅</div>
            <h2>Your crew request has been submitted successfully!</h2>
            <p>Thank you, {$company}.<br>Our team will review your request and get back to you soon.</p>
            <a href='find-crew.html' class='btn'>Go Back</a>
          </div>
        </body>
        </html>
        ";
    } catch (Exception $e) {
        // ❌ Error Page
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
          <meta charset='UTF-8'>
          <meta name='viewport' content='width=device-width, initial-scale=1.0'>
          <title>Submission Failed</title>
          <style>
            body {font-family: Arial, sans-serif; background:#f7f9fc; display:flex; justify-content:center; align-items:center; height:100vh; margin:0;}
            .box {background:#fff; padding:2rem; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); text-align:center; max-width:450px;}
            .box h2 {color:#c0392b;}
            .btn {display:inline-block; margin-top:1.5rem; padding:0.75rem 1.5rem; background:#c0392b; color:#fff; text-decoration:none; border-radius:8px;}
            .btn:hover {background:#a93226;}
            .error {margin-top:1rem; color:#555;}
          </style>
        </head>
        <body>
          <div class='box'>
            <div style='font-size:3rem;'>❌</div>
            <h2>Submission Failed</h2>
            <p>Sorry, your crew request could not be sent at this time.</p>
            <p class='error'>Error: {$e->getMessage()}</p>
            <a href='find-crew.html' class='btn'>Try Again</a>
          </div>
        </body>
        </html>
        ";
    }
}
?>
