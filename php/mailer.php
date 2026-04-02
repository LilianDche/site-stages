<?php
/**
 * Mailer – envoi d'email via SMTP natif PHP (sans dépendance externe)
 *
 * Configuration dans php/config.php :
 *   define('MAIL_HOST',     'smtp.gmail.com');   // serveur SMTP
 *   define('MAIL_PORT',     587);                // 587 (STARTTLS) ou 465 (SSL)
 *   define('MAIL_USERNAME', 'votre@gmail.com');  // identifiant SMTP
 *   define('MAIL_PASSWORD', 'motdepasse_app');   // mot de passe ou App Password
 *   define('MAIL_FROM',     'votre@gmail.com');  // adresse expéditeur
 *   define('MAIL_FROM_NAME','Web4All');          // nom affiché
 */

class Mailer {

    private string $host;
    private int    $port;
    private string $username;
    private string $password;
    private string $from;
    private string $fromName;
    private bool   $useTLS;

    public function __construct() {
        $this->host     = defined('MAIL_HOST')      ? MAIL_HOST      : 'smtp.gmail.com';
        $this->port     = defined('MAIL_PORT')       ? (int)MAIL_PORT : 587;
        $this->username = defined('MAIL_USERNAME')   ? MAIL_USERNAME  : '';
        $this->password = defined('MAIL_PASSWORD')   ? MAIL_PASSWORD  : '';
        $this->from     = defined('MAIL_FROM')       ? MAIL_FROM      : 'noreply@web4all.fr';
        $this->fromName = defined('MAIL_FROM_NAME')  ? MAIL_FROM_NAME : 'Web4All';
        // STARTTLS sur 587, SSL direct sur 465
        $this->useTLS   = ($this->port !== 465);
    }

    /**
     * Envoie un email.
     * @return bool  true si envoyé avec succès
     */
    public function send(string $to, string $subject, string $bodyText, string $bodyHtml = ''): bool {
        try {
            // Connexion socket
            if ($this->port === 465) {
                $socket = fsockopen('ssl://' . $this->host, $this->port, $errno, $errstr, 10);
            } else {
                $socket = fsockopen($this->host, $this->port, $errno, $errstr, 10);
            }
            if (!$socket) {
                error_log("[Mailer] Connexion échouée : $errstr ($errno)");
                return false;
            }

            $this->expect($socket, 220);

            // EHLO
            $this->send_cmd($socket, 'EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
            $this->read($socket); // lire la réponse multi-ligne EHLO

            // STARTTLS si nécessaire
            if ($this->useTLS) {
                $this->send_cmd($socket, 'STARTTLS');
                $this->expect($socket, 220);
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                // Ré-EHLO après TLS
                $this->send_cmd($socket, 'EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
                $this->read($socket);
            }

            // Auth LOGIN
            $this->send_cmd($socket, 'AUTH LOGIN');
            $this->expect($socket, 334);
            $this->send_cmd($socket, base64_encode($this->username));
            $this->expect($socket, 334);
            $this->send_cmd($socket, base64_encode($this->password));
            $this->expect($socket, 235);

            // Enveloppe
            $this->send_cmd($socket, 'MAIL FROM:<' . $this->from . '>');
            $this->expect($socket, 250);
            $this->send_cmd($socket, 'RCPT TO:<' . $to . '>');
            $this->expect($socket, 250);

            // Corps
            $this->send_cmd($socket, 'DATA');
            $this->expect($socket, 354);

            $boundary = 'b_' . md5(uniqid());
            $hasHtml  = trim($bodyHtml) !== '';

            $headers  = 'From: =?UTF-8?B?' . base64_encode($this->fromName) . '?= <' . $this->from . ">\r\n";
            $headers .= 'To: ' . $to . "\r\n";
            $headers .= 'Subject: =?UTF-8?B?' . base64_encode($subject) . "?=\r\n";
            $headers .= 'MIME-Version: 1.0' . "\r\n";

            if ($hasHtml) {
                $headers .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '"' . "\r\n";
                $body  = '--' . $boundary . "\r\n";
                $body .= "Content-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
                $body .= chunk_split(base64_encode($bodyText)) . "\r\n";
                $body .= '--' . $boundary . "\r\n";
                $body .= "Content-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
                $body .= chunk_split(base64_encode($bodyHtml)) . "\r\n";
                $body .= '--' . $boundary . '--';
            } else {
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n";
                $body     = chunk_split(base64_encode($bodyText));
            }

            fwrite($socket, $headers . "\r\n" . $body . "\r\n.\r\n");
            $this->expect($socket, 250);

            $this->send_cmd($socket, 'QUIT');
            fclose($socket);
            return true;

        } catch (\Exception $e) {
            error_log('[Mailer] ' . $e->getMessage());
            return false;
        }
    }

    // ── helpers ─────────────────────────────────────────────────

    private function send_cmd($socket, string $cmd): void {
        fwrite($socket, $cmd . "\r\n");
    }

    private function read($socket): string {
        $out = '';
        while ($line = fgets($socket, 512)) {
            $out .= $line;
            if (substr($line, 3, 1) === ' ') break; // fin du multi-ligne
        }
        return $out;
    }

    private function expect($socket, int $code): string {
        $response = $this->read($socket);
        $actual   = (int) substr($response, 0, 3);
        if ($actual !== $code) {
            throw new \RuntimeException("[Mailer] Attendu $code, reçu $actual : " . trim($response));
        }
        return $response;
    }
}
