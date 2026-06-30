<?php
header('Content-Type: application/json');

// Vključimo PHPMailer
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Nastavitev zaščite pred napadami
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metoda ni dovoljena']);
    exit;
}

// Preverjanje CSRF žetona
session_start();
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['error' => 'Neveljaven CSRF žeton']);
    exit;
}

// Preverjanje honeypot polja
if (!empty($_POST['website'])) {
    // Verjetno je bot, tiho zavrni
    echo json_encode(['success' => true]);
    exit;
}

// Preverjanje in čiščenje vhodnih podatkov
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

// Validacija podatkov
$errors = [];

if (empty($name) || strlen($name) < 2) {
    $errors[] = 'Ime mora vsebovati vsaj 2 znaka';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Vnesite veljaven e-poštni naslov';
}

if (empty($message) || strlen($message) < 10) {
    $errors[] = 'Sporočilo mora vsebovati vsaj 10 znakov';
}

// Če so napake, jih vrnemo
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit;
}

// Priprava podatkov za shranjevanje
$contactData = [
    'name' => $name,
    'email' => $email,
    'message' => $message,
    'ip' => $_SERVER['REMOTE_ADDR'],
    'timestamp' => date('Y-m-d H:i:s'),
    'user_agent' => $_SERVER['HTTP_USER_AGENT']
];

// Shranjevanje v JSON datoteko
$filePath = '../datoteke/orakleum/komentarji.json';

// Preberemo obstoječe podatke
$existingData = [];
if (file_exists($filePath)) {
    $jsonData = file_get_contents($filePath);
    $existingData = json_decode($jsonData, true) ?: [];
}

// Dodamo nove podatke
$existingData[] = $contactData;

// Shranimo nazaj v datoteko
if (file_put_contents($filePath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    
    // Pošljemo email obvestilo s PHPMailerjem
    $mail = new PHPMailer(true);
    
    try {
        // Nastavitve strežnika - PRILAGODITE TE NASTAVITVE!
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Nastavite svoj SMTP strežnik
        $mail->SMTPAuth = true;
        $mail->Username = 'vas.email@gmail.com'; // SMTP uporabniško ime
        $mail->Password = 'vase-geslo';         // SMTP geslo
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Prejemniki
        $mail->setFrom('no-reply@astramentalica.com', 'AstraMentalica');
        $mail->addAddress('info@astramentalica.com', 'AstraMentalica');
        $mail->addReplyTo($email, $name);
        
        // Vsebina
        $mail->isHTML(true);
        $mail->Subject = 'Novo sporočilo od ' . $name;
        $mail->Body = "
            <h2>Novo sporočilo prek spletne strani</h2>
            <p><strong>Ime:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Sporočilo:</strong></p>
            <p>{$message}</p>
            <hr>
            <p><small>IP naslov: {$_SERVER['REMOTE_ADDR']}<br>
            Čas: " . date('Y-m-d H:i:s') . "</small></p>
        ";
        
        $mail->AltBody = "Novo sporočilo od {$name} ({$email}):\n\n{$message}\n\nIP: {$_SERVER['REMOTE_ADDR']}\nČas: " . date('Y-m-d H:i:s');
        
        $mail->send();
        
        echo json_encode(['success' => true, 'redirect' => 'hvala_kontakt.html']);
        
    } catch (Exception $e) {
        // Če pošiljanje e-pošte spodleti, se podatki še vedno shranijo
        error_log("Napaka pri pošiljanju e-pošte: " . $mail->ErrorInfo);
        echo json_encode(['success' => true, 'redirect' => 'hvala_kontakt.html']);
    }
    
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Napaka pri shranjevanju podatkov']);
}
?>