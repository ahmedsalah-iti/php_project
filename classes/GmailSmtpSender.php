<?php

class GmailSmtpSender {
    private $smtpHost = "smtp.gmail.com";
    private $smtpPort = 587;
    private $username = "ahmedsalah.iti@gmail.com";
    private $password = "kzle mtkg ggen ozor";
    private $fromEmail = "ahmedsalah.iti@gmail.com";
    private $fromName = "Cafeteria Team";
    private $socket;

    public function __construct() {
    }

    private function connect() {
        $this->socket = fsockopen($this->smtpHost, $this->smtpPort, $errno, $errstr, 30);
        if (!$this->socket) {
            throw new Exception("Connection failed: $errstr ($errno)");
        }
        $this->getResponse();
    }

    private function getResponse() {
        $response = "";
        while ($line = fgets($this->socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == " ") break;
        }
        return $response;
    }

    private function sendCommand($command, $validate = true) {
        fputs($this->socket, $command . "\r\n");
        $response = $this->getResponse();
        if ($validate && strpos($response, "220") !== 0 && strpos($response, "235") !== 0 && 
            strpos($response, "250") !== 0 && strpos($response, "354") !== 0 && 
            strpos($response, "334") !== 0) {
            throw new Exception("Server error: $response");
        }
        return $response;
    }

    private function authenticate() {
        $this->sendCommand("EHLO localhost");
        $this->sendCommand("STARTTLS");
        if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new Exception("Failed to enable TLS encryption");
        }
        $this->sendCommand("EHLO localhost");
        $this->sendCommand("AUTH LOGIN");
        $this->sendCommand(base64_encode($this->username));
        $this->sendCommand(base64_encode($this->password));
    }

    public function send($toEmail, EmailTemplate $template) {
        try {
            $this->connect();
            $this->authenticate();

            $this->sendCommand("MAIL FROM:<{$this->fromEmail}>");
            $this->sendCommand("RCPT TO:<$toEmail>");
            $this->sendCommand("DATA");

            $headers = "From: {$this->fromName} <{$this->fromEmail}>\r\n";
            $headers .= "Reply-To: support@cafeteria.com\r\n";
            $headers .= "List-Unsubscribe: <https://cafeteria.iti.cam/unsubscribe>\r\n";
            $headers .= "To: $toEmail\r\n";
            $headers .= "Subject: {$template->getSubject()}\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            $this->sendCommand($headers . "\r\n" . $template->render() . "\r\n.");

            $this->sendCommand("QUIT", false);
            fclose($this->socket);

            return "Email sent successfully!";
        } catch (Exception $e) {
            if ($this->socket) {
                fclose($this->socket);
            }
            return "Failed to send email: " . $e->getMessage();
        }
    }
}