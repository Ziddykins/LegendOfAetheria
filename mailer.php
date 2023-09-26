<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';
    

    function send_mail($email) {
        try {
            global $account;
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();         
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPAuth   = true;
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->Username   = $_ENV['SMTP_USER'];
            $mail->Password   = $_ENV['SMTP_PASS'];
            $mail->Port       = $_ENV['SMTP_PORT'];

            $mail->setFrom('noreply@legendofaetheria.ca', 'Legend Of Aetheria');
            $mail->addReplyTo('noreply@legendofaetheria.ca', 'Legend Of Aetheria');
            
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Verify Your LoA Account';
            $mail->Body    = 'Click: <a href="/verification.php?code=' . $account['verification_code'] . '">Verify</a>';
            $mail->AltBody = 'fuk u skrubs get real email';
        
            $mail->send();
            echo $mail->SMTPDebug;
            die();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
?>