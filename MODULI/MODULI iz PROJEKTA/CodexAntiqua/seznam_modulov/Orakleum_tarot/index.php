
<?php


// Pot do JSON datoteke
$dataFile = 'data/readings.json';

// Funkcija za branje JSON datoteke
function readTarotReadings() {
    global $dataFile;
    if (file_exists($dataFile)) {
        $json = file_get_contents($dataFile);
        return json_decode($json, true);
    }
    return ['readings' => []];
}

// Funkcija za pisanje v JSON datoteke
function writeTarotReadings($data) {
    global $dataFile;
    // Preverimo, ali mapa data obstaja
    if (!is_dir('data')) {
        mkdir('data', 0755, true);
    }
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
}

// Obdelava zahteve za vlečenje kart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['draw_cards'])) {
    $data = readTarotReadings();
    
    // Definicija tarot kart
    $tarotCards = [
        ['name' => 'The Fool', 'meaning' => 'Začetek, nedolžnost, spontanost', 'image' => 'fool.jpg'],
        ['name' => 'The Magician', 'meaning' => 'Ustvarjalnost, moč, manifestacija', 'image' => 'magician.jpg'],
        ['name' => 'The High Priestess', 'meaning' => 'Intuicija, skrivnost, modrost', 'image' => 'priestess.jpg'],
        ['name' => 'The Empress', 'meaning' => 'Plodnost, lepota, narava', 'image' => 'empress.jpg'],
        ['name' => 'The Emperor', 'meaning' => 'Avtoriteta, struktura, nadzor', 'image' => 'emperor.jpg'],
        ['name' => 'The Hierophant', 'meaning' => 'Tradicija, duhovnost, vernost', 'image' => 'hierophant.jpg'],
        ['name' => 'The Lovers', 'meaning' => 'Ljubezen, harmonija, odločitve', 'image' => 'lovers.jpg'],
        ['name' => 'The Chariot', 'meaning' => 'Zmotivacija, odločnost, uspeh', 'image' => 'chariot.jpg'],
        ['name' => 'Strength', 'meaning' => 'Pogum, moč, notranja moč', 'image' => 'strength.jpg'],
        ['name' => 'The Hermit', 'meaning' => 'Introspekcija, iskanje resnice', 'image' => 'hermit.jpg']
    ];
    
    // Naključno izberemo 3 karte
    $drawnCards = [];
    $selectedIndices = array_rand($tarotCards, 3);
    foreach ((array)$selectedIndices as $index) {
        $drawnCards[] = $tarotCards[$index];
    }
    
    // Shranimo vedeževanje
    $newReading = [
        'id' => uniqid(),
        'user_id' => $_SESSION['user_id'],
        'cards' => $drawnCards,
        'timestamp' => date('Y-m-d H:i:s'),
        'interpretation' => generateInterpretation($drawnCards)
    ];
    
    $data['readings'][] = $newReading;
    writeTarotReadings($data);
    
    // Prikažemo rezultate
    $result = $newReading;
}

// Funkcija za generiranje interpretacije
function generateInterpretation($cards) {
    $meanings = array_map(function($card) {
        return $card['meaning'];
    }, $cards);
    
    return "Vaše vedeževanje kaže na " . implode(", ", $meanings) . ". To je čas za refleksijo in osebno rast.";
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarot Igralnica - Orakleum</title>
    <link rel="stylesheet" href="tarot.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Tarot Igralnica</h1>
            <p>Vlecite karte in odkrijte svojo usodo</p>
        </header>
        
        <nav>
            <a href="../index.php">Nazaj na Orakleum</a>
            <a href="../../index.php">Na Astramentalico</a>
        </nav>
        
        <main>
            <?php if (isset($result)): ?>
            <div class="reading-result">
                <h2>Vaše vedeževanje</h2>
                <p class="timestamp">Datum: <?php echo $result['timestamp']; ?></p>
                
                <div class="cards-container">
                    <?php foreach ($result['cards'] as $card): ?>
                    <div class="tarot-card">
                        <div class="card-image">
                            <img src="images/<?php echo $card['image']; ?>" alt="<?php echo htmlspecialchars($card['name']); ?>">
                        </div>
                        <h3><?php echo htmlspecialchars($card['name']); ?></h3>
                        <p><?php echo htmlspecialchars($card['meaning']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="interpretation">
                    <h3>Interpretacija</h3>
                    <p><?php echo $result['interpretation']; ?></p>
                </div>
                
                <div class="actions">
                    <a href="index.php" class="btn">Vleci znova</a>
                    <a href="../index.php" class="btn">Nazaj na Orakleum</a>
                </div>
            </div>
            <?php else: ?>
            <div class="draw-section">
                <h2>Vleci tarot karte</h2>
                <p>Kliknite na gumb spodaj, da izvlečete tri tarot karte in prejmete osebno vedeževanje.</p>
                
                <form method="POST">
                    <button type="submit" name="draw_cards" class="btn draw-btn">Vleci karte</button>
                </form>
            </div>
            <?php endif; ?>
        </main>
        
        <footer>
            <p>&copy; 2024 Astramentalica - Tarot Igralnica</p>
        </footer>
    </div>
</body>
</html>
