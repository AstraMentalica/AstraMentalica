<?php
header('Content-Type: application/json');

// Nastavitev zaščite pred napadami
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metoda ni dovoljena']);
    exit;
}

// Preverjanje CSRF žetona (če je potrebno)
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
$branje = filter_input(INPUT_POST, 'branje', FILTER_SANITIZE_STRING);
$donacija = isset($_POST['donacija']) ? true : false;

// Validacija podatkov
$errors = [];

if (empty($name) || strlen($name) < 2) {
    $errors[] = 'Ime mora vsebovati vsaj 2 znaka';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Vnesite veljaven e-poštni naslov';
}

$validna_branja = [
    'hitro', 'ljubezen', 'kariera', 'senca', 'boginje', 
    'karma', 'pretekla', 'energetsko', 'pdf', 
    'dusni_par', 'astro', 'svetovanje', 'celostno'
];

if (empty($branje) || !in_array($branje, $validna_branja)) {
    $errors[] = 'Izberite veljavno vrsto branja';
}

// Če so napake, jih vrnemo
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit;
}

// Priprava podatkov za shranjevanje
$orderData = [
    'name' => $name,
    'email' => $email,
    'branje' => $branje,
    'donacija' => $donacija,
    'ip' => $_SERVER['REMOTE_ADDR'],
    'timestamp' => date('Y-m-d H:i:s'),
    'status' => 'novo'
];

// Shranjevanje v JSON datoteko
$filePath = '../datoteke/orakleum/narocila.json';

// Preberemo obstoječe podatke
$existingData = [];
if (file_exists($filePath)) {
    $jsonData = file_get_contents($filePath);
    $existingData = json_decode($jsonData, true) ?: [];
}

// Dodamo nove podatke
$existingData[] = $orderData;

// Shranimo nazaj v datoteko
if (file_put_contents($filePath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    // Pošljemo email obvestilo (če je nastavljeno)
    $to = 'info@astramentalica.com';
    $subject = 'Novo naročilo od ' . $name;
    
    $branjeMap = [
        'hitro' => 'Hitro vprašanje (9 €)',
        'ljubezen' => 'Ljubezensko branje (18 €)',
        'kariera' => 'Kariera & Finance (18 €)',
        'senca' => 'Senca & notranje blokade (18 €)',
        'boginje' => 'Zmaji, boginje, vile (18 €)',
        'karma' => 'Dušna pot & karma (27 €)',
        'pretekla' => 'Pretekla življenja (27 €)',
        'energetsko' => 'Energetska diagnostika (27 €)',
        'pdf' => 'Mesečni PDF vodnik (33 €)',
        'dusni_par' => 'Dušni odnosi (33 €)',
        'astro' => 'Astro-numerološki vpogled (42 €)',
        'svetovanje' => 'Zoom svetovanje (42 €)',
        'celostno' => 'Celostno intuitivno branje (54 €)'
    ];
    
    $emailMessage = "Prejeli ste novo naročilo:\n\n";
    $emailMessage .= "Ime: $name\n";
    $emailMessage .= "Email: $email\n";
    $emailMessage .= "Branje: " . ($branjeMap[$branje] ?? $branje) . "\n";
    $emailMessage .= "Donacija: " . ($donacija ? 'Da' : 'Ne') . "\n\n";
    $emailMessage .= "IP naslov: {$_SERVER['REMOTE_ADDR']}\n";
    $emailMessage .= "Čas: " . date('Y-m-d H:i:s');
    
    $headers = "From: no-reply@astramentalica.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    
    // Pošljemo email (v praksi bi uporabili boljšo metodo kot mail())
    // mail($to, $subject, $emailMessage, $headers);
    
    // Preusmeritev na zahvalno stran s podatki
    $redirectUrl = "hvala.html?branje=$branje&ime=" . urlencode($name) . "&email=" . urlencode($email);
    echo json_encode(['success' => true, 'redirect' => $redirectUrl]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Napaka pri shranjevanju naročila']);
}
?>