<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

function sendEmailNotification($to_email, $subject, $message_body) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'webcomputer06@gmail.com'; 
        $mail->Password   = 'zqsc tnzs igij ylcr'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('webcomputer06@gmail.com', 'Election Commission');
        $mail->addAddress($to_email);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message_body;

        return $mail->send();
    } catch (Exception $e) {
        // ইমেল কেন সেন্ড হচ্ছে না তার আসল কারণ দেখার জন্য
        echo "Mailer Error: " . $mail->ErrorInfo;
        exit();
    }
}
?>