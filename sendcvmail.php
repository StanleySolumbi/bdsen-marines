<?php
// Hide PHP errors from users (log them instead)
error_reporting(E_ALL);
ini_set('display_errors', 0);

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'] ?? '';
    $email    = $_POST['email'] ?? '';
    $position = $_POST['position'] ?? '';

    $mail = new PHPMailer(true);

    try {
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host       = 'bdsen-marines.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'crewinfo@bdsen-marines.com';
        $mail->Password   = 'Stanley_08069795682'; // ⚠️ Consider storing securely!
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Sender & Recipient
        $mail->setFrom('crewinfo@bdsen-marines.com', 'BDS Energy & Marine Careers');
        $mail->addAddress('crewinfo@bdsen-marines.com');
        if (!empty($email)) {
            $mail->addReplyTo($email, $name);
        }

        // Attachment
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
            $mail->addAttachment($_FILES['attachment']['tmp_name'], $_FILES['attachment']['name']);
        }

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = "New CV Submission for {$position} - {$name}";
        $mail->Body    = "
            <h3>New Job Application Received</h3>
            <p><b>Name:</b> {$name}</p>
            <p><b>Email:</b> {$email}</p>
            <p><b>Position Applied For:</b> {$position}</p>
            <p>The applicant's CV is attached.</p>
        ";

        // Disable debugging in production
        $mail->SMTPDebug = 0;

        $mail->send();

        // Success page
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
          <meta charset='UTF-8'>
          <meta name='viewport' content='width=device-width, initial-scale=1.0'>
          <title>Application Submitted</title>
          <style>
            body {
              font-family: Arial, sans-serif;
              background: #f7f9fc;
              display: flex;
              justify-content: center;
              align-items: center;
              height: 100vh;
              margin: 0;
            }
            .box {
              background: #fff;
              padding: 2rem;
              border-radius: 12px;
              box-shadow: 0 4px 12px rgba(0,0,0,0.1);
              text-align: center;
              max-width: 400px;
            }
            .box h2 {
              color: #2b7a2b;
            }
            .btn {
              display: inline-block;
              margin-top: 1.5rem;
              padding: 0.75rem 1.5rem;
              background: #2b7a2b;
              color: #fff;
              text-decoration: none;
              border-radius: 8px;
              transition: background 0.3s;
            }
            .btn:hover {
              background: #256b25;
            }
          </style>
        </head>
        <body>
          <div class='box'>
            <div style='font-size:3rem;'>✅</div>
            <h2>Your CV has been submitted successfully!</h2>
            <p>Thank you, {$name}.<br>We will review your application and get back to you soon.</p>
            <a href='upload-cv.html' class='btn'>Go Back</a>
          </div>
        </body>
        </html>
        ";
    } catch (Exception $e) {
        // Error page (generic message)
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
          <meta charset='UTF-8'>
          <meta name='viewport' content='width=device-width, initial-scale=1.0'>
          <title>Submission Failed</title>
          <style>
            body {
              font-family: Arial, sans-serif;
              background: #f7f9fc;
              display: flex;
              justify-content: center;
              align-items: center;
              height: 100vh;
              margin: 0;
            }
            .box {
              background: #fff;
              padding: 2rem;
              border-radius: 12px;
              box-shadow: 0 4px 12px rgba(0,0,0,0.1);
              text-align: center;
              max-width: 400px;
            }
            .box h2 {
              color: #c0392b;
            }
            .btn {
              display: inline-block;
              margin-top: 1.5rem;
              padding: 0.75rem 1.5rem;
              background: #c0392b;
              color: #fff;
              text-decoration: none;
              border-radius: 8px;
              transition: background 0.3s;
            }
            .btn:hover {
              background: #a93226;
            }
          </style>
        </head>
        <body>
          <div class='box'>
            <div style='font-size:3rem;'>❌</div>
            <h2>Submission Failed</h2>
            <p>Sorry, your application could not be sent at this time.<br>Please try again later.</p>
            <a href='upload-cv.html' class='btn'>Try Again</a>
          </div>
        </body>
        </html>
        ";
    }
}
?>
