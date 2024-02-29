<?php
/**
 * @desc    : 邮件服务类
 * @author  : sam
 * @email   : sam@imccc.cc
 * @date    : 2024/2/26 19:20
 * @version : 1.0.0
 * @license : MIT
 */

namespace Imccc\Slim\Service;

class MailerService
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $encryption;

    public function __construct($host, $port, $username, $password, $encryption = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->encryption = $encryption;
    }

    public function send($to, $subject, $body, $from = null, $replyTo = null, $attachments = [])
    {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
        ];

        if ($from !== null) {
            $headers[] = 'From: ' . $from;
        } else {
            $headers[] = 'From: ' . $this->username;
        }

        if ($replyTo !== null) {
            $headers[] = 'Reply-To: ' . $replyTo;
        } else {
            $headers[] = 'Reply-To: ' . $this->username;
        }

        $headers[] = 'X-Mailer: PHP/' . phpversion();

        if (!empty($attachments)) {
            $boundary = uniqid('np');
            $headers[] = 'Content-type: multipart/mixed; boundary=' . $boundary;

            $message = "--" . $boundary . "\r\n";
            $message .= "Content-type: text/html; charset=utf-8\r\n";
            $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $message .= $body . "\r\n\r\n";

            foreach ($attachments as $attachment) {
                $filename = $attachment['filename'];
                $content = $attachment['content'];
                $type = $attachment['type'];

                $message .= "--" . $boundary . "\r\n";
                $message .= "Content-Type: $type; name=\"$filename\"\r\n";
                $message .= "Content-Disposition: attachment; filename=\"$filename\"\r\n";
                $message .= "Content-Transfer-Encoding: base64\r\n";
                $message .= "X-Attachment-Id: " . uniqid() . "\r\n\r\n";
                $message .= chunk_split(base64_encode($content)) . "\r\n";
            }

            $message .= "--" . $boundary . "--";
        } else {
            $message = $body;
        }

        $additional_parameters = null;

        if ($this->encryption === 'ssl') {
            $additional_parameters = '-f' . $this->username;
        }

        return mail($to, $subject, $message, implode("\r\n", $headers), $additional_parameters);
    }
}

// 使用示例
// $mailer = new MailerService('smtp.example.com', 587, 'username@example.com', 'password', 'tls');

// $to = 'recipient@example.com';
// $subject = 'Test Email';
// $body = 'This is a test email from the MailerService class.';

// if ($mailer->send($to, $subject, $body)) {
//     echo "Email sent successfully to $to";
// } else {
//     echo "Failed to send email";
// }
