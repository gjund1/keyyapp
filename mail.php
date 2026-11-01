<?php

// In Terminal : composer require phpmailer/phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Envoie un email via SMTP.
 *
 * @param string $to        Adresse du destinataire
 * @param string $subject   Sujet du mail
 * @param string $body      Contenu HTML du mail
 *
 * @return bool
 */

function sendMail($to, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP
        //===================================================
        $mail->isSMTP();                                    // Utilisation de SMTP
        $mail->Host       = 'keyyapp.com';                  // -> nom du serveur SMTP kjtr4827
        $mail->SMTPAuth   = true;                           // activation de l'authentification SMTP
        $mail->Username   = 'info@keyyapp.com';             // votre adresse email
        $mail->Password   = 'TON_VRAI_MOT_DE_PASSE';        // -> votre mot de passe réel pour l'adresse email
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;    // SSL/TLS
        $mail->Port       = 465;                            // port SMTP pour SSL
        //===================================================

        // Expéditeur
        $mail->setFrom('info@keyyapp.com', 'KeyyApp');

        // Destinataire
        $mail->addAddress($to);

        // Contenu
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        // Envoi
        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log('Erreur envoi email : ' . $mail->ErrorInfo);
        return false;
    }
}