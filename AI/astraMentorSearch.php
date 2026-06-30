<?php
require_once 'varnost.php';
require_once 'astraMentor/astraMentorHistory.php';

$user_id = $_GET['user_id'] ?? '';
$search = $_GET['q'] ?? '';
$results = [];

if ($user_id && $search) {
    $results = search_conversations($user_id, $search);
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
<meta charset="UTF-8">
<title>Iskanje pogovorov</title>
</head>
<body>
<h2>Iskanje pogovorov za uporabnika: <?= htmlspecialchars($user_id) ?></h2>

<form method="get">
    <input type="text" name="user_id" placeholder="User ID" value="<?= htmlspecialchars($user_id) ?>">
    <input type="text" name="q" placeholder="Iskan niz" value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Išči</button>
</form>

<?php if ($results): ?>
<h3>Rezultati:</h3>
<ul>
<?php foreach($results as $r): ?>
    <li>
        <b>User:</b> <?= htmlspecialchars($r['user_message']) ?><br>
        <b>AI:</b> <?= htmlspecialchars($r['ai_response']) ?><br>
        <i><?= date('Y-m-d H:i:s', $r['timestamp']) ?></i>
    </li>
<?php endforeach; ?>
</ul>
<?php elseif ($search): ?>
<p>Ni najdenih rezultatov.</p>
<?php endif; ?>
</body>
</html>