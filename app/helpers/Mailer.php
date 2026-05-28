<?php
require_once __DIR__ . '/../../config/app.php';

// Si Composer autoload está disponible, cargarlo para detectar PHPMailer
$composerAutoload = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

class Mailer
{
    /**
     * Enviar correo usando PHPMailer si está disponible, si no, usar mail().
     * @param string $to
     * @param string $subject
     * @param string $body
     * @return bool
     */
    public static function send(string $to, string $subject, string $body): bool
    {
        // Si PHPMailer está instalado, usarlo (más fiable para SMTP)
        if (class_exists('\PHPMailer\\PHPMailer\\PHPMailer')) {
            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->SMTPDebug = 0;
                $mail->Debugoutput = function ($str, $level) {
                    error_log('[Mailer][DEBUG] ' . trim($str));
                };

                if (MAILER_SMTP_ENABLED) {
                    $mail->isSMTP();
                    $mail->Host = MAILER_HOST;
                    $mail->SMTPAuth = true;
                    $mail->Username = MAILER_USERNAME;
                    $mail->Password = MAILER_PASSWORD;
                    $mail->SMTPSecure = MAILER_SMTP_SECURE;
                    $mail->Port = (int)MAILER_PORT;
                    $mail->SMTPAutoTLS = false;
                    $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true,
                        ],
                    ];
                }

                $mail->setFrom(MAILER_FROM, MAILER_FROM_NAME);
                $mail->addAddress($to);
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->isHTML(false);

                return $mail->send();
            } catch (\PHPMailer\PHPMailer\Exception $e) {
                error_log('[Mailer] PHPMailer send error: ' . $e->getMessage());
            } catch (\Exception $e) {
                error_log('[Mailer] General mail error: ' . $e->getMessage());
            }
        }

        // Fallback simple a mail()
        $headers = 'From: ' . MAILER_FROM . "\r\n" . 'Reply-To: ' . MAILER_FROM . "\r\n";
        return mail($to, $subject, $body, $headers);
    }
}
