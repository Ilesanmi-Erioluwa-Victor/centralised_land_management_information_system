<?php

namespace App\Helpers;

use App\Config\Env;
use PHPMailer\PHPMailer\PHPMailer;
use Throwable;

/**
 * Sends transactional email through Brevo SMTP.
 */
class Mailer
{
    /**
     * Send an HTML email with a plain-text fallback.
     *
     * @param string $to
     * @param string $toName
     * @param string $subject
     * @param string $htmlBody
     * @param string $plainText
     * @return bool
     */
    public static function send(string $to, string $toName, string $subject, string $htmlBody, string $plainText = ''): bool
    {
        if (!class_exists(PHPMailer::class)) {
            error_log('PHPMailer is not installed. Email skipped: ' . $subject);
            return false;
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = Env::get('MAIL_HOST', 'smtp-relay.brevo.com');
            $mail->SMTPAuth = true;
            $mail->Username = Env::get('MAIL_USERNAME');
            $mail->Password = Env::get('MAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int) Env::get('MAIL_PORT', 587);
            $mail->setFrom(Env::get('MAIL_FROM', 'noreply@example.com'), Env::get('MAIL_FROM_NAME', 'CLMIS System'));
            $mail->addAddress($to, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $plainText ?: strip_tags($htmlBody);
            return $mail->send();
        } catch (Throwable $e) {
            error_log('Brevo email failed: ' . $e->getMessage());
            return false;
        }
    }
}
