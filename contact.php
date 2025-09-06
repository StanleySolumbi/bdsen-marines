<?php
// Hide PHP errors from users (log instead)
error_reporting(E_ALL);
ini_set('display_errors', 0);

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $_POST['name'] ?? '';
    $email   = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    $mail = new PHPMailer(true);

    try {
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host       = 'bdsen-marines.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@bdsen-marines.com';
        $mail->Password   = 'Stanley_08069795682'; // ⚠️ store securely in production!
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Sender & Recipient
        $mail->setFrom('info@bdsen-marines.com', 'BDS Energy & Marine - Contact Form');
        $mail->addAddress('info@bdsen-marines.com');
        if (!empty($email)) {
            $mail->addReplyTo($email, $name);
        }

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Message from {$name}";
        $mail->Body    = "
            <h3>New Message Received</h3>
            <p><b>Name:</b> {$name}</p>
            <p><b>Email:</b> {$email}</p>
            <p><b>Message:</b><br>" . nl2br(htmlspecialchars($message)) . "</p>
        ";

        $mail->SMTPDebug = 0; // disable debug in production

        $mail->send();

        // ✅ Success Page
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
          <meta charset='UTF-8'>
          <meta name='viewport' content='width=device-width, initial-scale=1.0'>
          <title>Message Sent</title>
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
            <h2>Your message has been sent!</h2>
            <p>Thank you, {$name}.<br>We will get back to you soon.</p>
            <a href='contact.html' class='btn'>Go Back</a>
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
          <title>Message Failed</title>
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
            <h2>Message Failed</h2>
            <p>Sorry, your message could not be sent at this time.<br>Please try again later.</p>
            <a href='contact.html' class='btn'>Try Again</a>
          </div>
        </body>
        </html>
        ";
    }
}
?>
