<?php
/**
 * Receives the enquiry form on contact.html and emails it using PHPMailer.
 */

use PHPMailer\PHPMailer\PHPMailer;

ini_set('display_errors', '0'); // never show server details to visitors

// Send the result back: JSON for the page script, a simple page if JavaScript is off.
function respond(int $status, bool $ok, string $message): void
{
    http_response_code($status);

    if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok, 'message' => $message]);
    } else {
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>Enquiry | Prashanth Home Solutions</title>'
            . '<body style="font-family:sans-serif;padding:24px"><p>' . htmlspecialchars($message) . '</p>'
            . '<p><a href="contact.html">Back to Contact Us</a></p>';
    }
    exit;
}

// Read one text field. Anything that is not a string (e.g. an array) counts as empty.
function field(string $name): string
{
    $value = $_POST[$name] ?? '';
    return is_string($value) ? trim($value) : '';
}

const FAILURE = 'Sorry, your enquiry could not be sent. Please call us on 9380989804 or message us on WhatsApp.';
const SUCCESS = 'Thank you! Your enquiry has been sent. We will contact you soon.';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    respond(405, false, 'Please use the form on the Contact Us page.');
}

// Spam trap: people never see this field, but many bots fill it in.
if (field('website') !== '') {
    respond(200, true, SUCCESS);
}

$name    = field('name');
$phone   = field('phone');
$service = field('service');
$message = field('message');

// Keep this list the same as the <select> options in contact.html.
$services = [
    'Electrical Works', 'Plumbing Works', 'Copper Gas Pipeline', 'False Ceiling',
    'Interior Works', 'Painting Works', 'Tiles Work', 'Water Proofing',
    'CCTV Installation', 'UPS / Inverter / Battery', 'Solar Water Heater',
    'Kitchen Renovation', 'Bathroom Renovation', 'Home / Flat Renovation',
    'Home Loans', 'Other',
];

// No line breaks or control characters are allowed in the name.
if (!preg_match('/^[^\p{Cc}]{1,80}$/u', $name)) {
    respond(422, false, 'Please enter your name (up to 80 characters).');
}
$digits = preg_replace('/\D/', '', $phone);
if (!preg_match('/^\+?[0-9 ()-]{7,20}$/D', $phone) || strlen($digits) < 7 || strlen($digits) > 15) {
    respond(422, false, 'Please enter a valid phone number.');
}
if (!in_array($service, $services, true)) {
    respond(422, false, 'Please choose a service from the list.');
}
if (!preg_match('/^.{0,2000}$/su', $message)) {
    respond(422, false, 'Please keep your message under 2000 characters.');
}

// Load PHPMailer: installed with Composer, or uploaded by hand into /PHPMailer.
if (is_file(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} elseif (is_file(__DIR__ . '/PHPMailer/src/PHPMailer.php')) {
    require __DIR__ . '/PHPMailer/src/Exception.php';
    require __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require __DIR__ . '/PHPMailer/src/SMTP.php';
} else {
    error_log('send-mail.php: PHPMailer is not installed.');
    respond(500, false, FAILURE);
}

if (!is_file(__DIR__ . '/mail-config.php')) {
    error_log('send-mail.php: mail-config.php is missing.');
    respond(500, false, FAILURE);
}
$config = require __DIR__ . '/mail-config.php';

try {
    $mail = new PHPMailer(true); // true = throw exceptions on errors

    $mail->isSMTP();
    $mail->Host       = $config['host'];
    $mail->Port       = $config['port'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['username'];
    $mail->Password   = $config['password'];
    $mail->SMTPSecure = $config['encryption']; // 'tls' (port 587) or 'ssl' (port 465)
    $mail->Timeout    = 15;
    $mail->CharSet    = 'UTF-8';

    // "From" must be your own mailbox, otherwise the mail server may reject it.
    $mail->setFrom($config['from_email'], $config['from_name']);
    $mail->addAddress($config['to_email']);

    $mail->isHTML(false); // plain text, so visitor input is never treated as HTML
    $mail->Subject = "Enquiry: $service - $name";
    $mail->Body    = "New enquiry from the website\n\n"
        . "Name: $name\n"
        . "Phone: $phone\n"
        . "Service: $service\n\n"
        . "Message:\n" . ($message !== '' ? $message : '(none)') . "\n";

    $mail->send();
} catch (\Exception $e) {
    error_log('send-mail.php: ' . $e->getMessage());
    respond(500, false, FAILURE);
}

respond(200, true, SUCCESS);
