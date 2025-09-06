<?php
// Hide PHP errors from users (log them instead in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// ✅ Load secure credentials from outside public_html
require '/home/bdsekqrh/secure-config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname    = $_POST['fullname'] ?? '';
    $email       = $_POST['email'] ?? '';
    $rank        = $_POST['rank'] ?? '';
    $availability= $_POST['availability'] ?? '';
    $vesselType  = $_POST['vesselType'] ?? '';

    $mail = new PHPMailer(true);

    try {
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS; // ⚠️ Use env/config file in production
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Sender & Recipient
        $mail->setFrom(SMTP_USER, 'BDS Energy & Marine - Job Application');
        $mail->addAddress(SMTP_USER);
        if (!empty($email)) {
            $mail->addReplyTo($email, $fullname);
        }

        // ✅ File Upload Validation (CV)
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] == UPLOAD_ERR_OK) {
            $allowedExtensions = ['pdf', 'doc', 'docx', 'rtf', 'odt', 'xls', 'xlsx'];
            $fileName = $_FILES['cv']['name'];
            $fileTmp  = $_FILES['cv']['tmp_name'];
            $fileSize = $_FILES['cv']['size'];
            $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($fileExt, $allowedExtensions)) {
                throw new Exception("Invalid file type. Allowed: PDF, Word, Excel, RTF, ODT.");
            }

            if ($fileSize > 2 * 1024 * 1024) { // 2MB max
                throw new Exception("File too large. Max size is 2MB.");
            }

            $mail->addAttachment($fileTmp, $fileName);
        }

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = "New Job Application from {$rank} - {$fullname}";
        $mail->Body    = "
            <h3>New Job Application Received</h3>
            <p><b>Full Name:</b> {$fullname}</p>
            <p><b>Email:</b> {$email}</p>
            <p><b>Rank / Position:</b> {$rank}</p>
            <p><b>Availability:</b> {$availability}</p>
            <p><b>Preferred Vessel Type:</b> {$vesselType}</p>
            <p><i>Applicant's CV is attached.</i></p>
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
          <title>Application Submitted</title>
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
            <h2>Your application has been submitted successfully!</h2>
            <p>Thank you, {$fullname}.<br>We will review your application and contact you soon.</p>
            <a href='find-job.html' class='btn'>Go Back</a>
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
            <p>Sorry, your application could not be sent.</p>
            <p class='error'>Error: {$e->getMessage()}</p>
            <a href='find-job.html' class='btn'>Try Again</a>
          </div>
        </body>
        </html>
        ";
    }
}
?>
