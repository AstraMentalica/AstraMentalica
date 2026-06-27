<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/administracija/avtomatika/obdelava.php
 * v111 (27.5.2026 07:30)
 * ---------------------------------------------------------
 * OPIS: Obdelava čakalne vrste (queue worker)
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 * - SISTEM/runtime/vrsta/vrsta_odprava.php
 * - SISTEM/administracija/avtomatika/opravila.php
 * - SISTEM/administracija/avtomatika/razpored.php
 * - SISTEM/administracija/avtomatika/cron.php
 *
 * UPORABA:
 * - cli.php (php cli.php worker)
 *
 * FUNKCIJE:
 * - obdelava_worker(), obdelava_daemon()
 * - obdelava_status(), obdelava_zagon_vseh_workerjev()
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump (razen CLI izpisa)
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 8 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

function obdelava_worker(string $vrsta = 'obicajna_prednost', int $cikelMs = 1000, bool $enkrat = false): void
{
// Registriraj opravila
if (function_exists('opravila_registriraj_vsa')) {
    opravila_registriraj_vsa();
}

// Registriraj razporejena opravila
if (function_exists('razpored_registriraj_privzeta')) {
    razpored_registriraj_privzeta();
}

$deluje = true;
$stevec = 0;
$zadnjeRazporejanje = time();

while ($deluje) {
    $zdaj = time();
    
    // Obdelaj pakete iz vrste
    if (function_exists('vrsta_odprava_obdelaj')) {
        $obdelanih = vrsta_odprava_obdelaj($vrsta, 5);
        if ($obdelanih > 0) {
            $stevec += $obdelanih;
            if (PHP_SAPI === 'cli') {
                echo "[" . date('Y-m-d H:i:s') . "] Obdelanih $obdelanih paketov (skupaj: $stevec)\n";
            }
        }
    }
    
    // Izvedi razporejena opravila (vsako minuto)
    if ($zdaj - $zadnjeRazporejanje >= 60) {
        if (function_exists('razpored_izvedi')) {
            $izvedena = razpored_izvedi();
            if (!empty($izvedena) && PHP_SAPI === 'cli') {
                echo "[" . date('Y-m-d H:i:s') . "] Izvedena razporejena opravila: " . implode(', ', $izvedena) . "\n";
            }
        }
        $zadnjeRazporejanje = $zdaj;
    }
    
    if ($enkrat) {
        break;
    }
    
    usleep($cikelMs * 1000);
}

if (PHP_SAPI === 'cli') {
    echo "[" . date('Y-m-d H:i:s') . "] Worker ustavljen. Skupaj obdelanih: $stevec\n";
}
}

function obdelava_daemon(string $vrsta = 'obicajna_prednost', int $cikelMs = 1000): void
{
if (PHP_SAPI !== 'cli') {
    echo "Daemon se lahko zažene samo iz CLI\n";
    return;
}

// Odklopimo se od terminala
if (function_exists('pcntl_fork')) {
    $pid = pcntl_fork();
    if ($pid == -1) {
        die("Ne morem forkati\n");
    } elseif ($pid) {
        exit(0);
    }
    posix_setsid();
}

// Preusmerimo izhod
fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);

$stdout = fopen('/dev/null', 'a');
$stderr = fopen('/dev/null', 'a');

// Zapiši PID v datoteko
$pidFile = POT_PODATKI . '/tmp/worker.pid';
file_put_contents($pidFile, getmypid());

obdelava_worker($vrsta, $cikelMs);

// Počisti PID datoteko ob koncu
if (file_exists($pidFile)) {
    unlink($pidFile);
}
}

function obdelava_status(): array
{
$status = [
    'workerji' => [],
    'cron' => [],
    'queue' => [],
    'razpored' => [],
    'cas' => time(),
    'cas_formatiran' => date('Y-m-d H:i:s')
];

// Queue status
if (function_exists('queue_statistika')) {
    $status['queue'] = queue_statistika();
}

// Cron status
if (function_exists('cron_statistika')) {
    $status['cron'] = cron_statistika();
}

// Razpored status
if (function_exists('razpored_statistika')) {
    $status['razpored'] = razpored_statistika();
}

// Preveri ali worker teče
$pidFile = POT_PODATKI . '/tmp/worker.pid';
if (file_exists($pidFile)) {
    $pid = (int)file_get_contents($pidFile);
    if (function_exists('posix_kill') && posix_kill($pid, 0)) {
        $status['workerji'][] = ['pid' => $pid, 'status' => 'teče'];
    } else {
        $status['workerji'][] = ['pid' => $pid, 'status' => 'ne deluje'];
        unlink($pidFile);
    }
}

return $status;
}

function obdelava_zagon_vseh_workerjev(): void
{
// Zaženi workerje za različne vrste (v produkciji kot ločeni procesi)
$vrste = ['sprotno', 'visoka_prednost', 'obicajna_prednost', 'nizka_prednost', 'elektronska_posta', 'casovnik'];

foreach ($vrste as $vrsta) {
    if (function_exists('obdelava_worker')) {
        // V produkciji bi se to zagnalo kot ločeni procesi
        dnevnik_info("Worker za vrsto '$vrsta' pripravljen");
    }
}

// Zaženi cron
if (function_exists('cron_zagon')) {
    cron_zagon();
    dnevnik_info("Cron zaganjalnik aktiviran");
}
}

function obdelava_ustavi_workerja(): bool
{
$pidFile = POT_PODATKI . '/tmp/worker.pid';
if (!file_exists($pidFile)) {
    return false;
}

$pid = (int)file_get_contents($pidFile);
if (function_exists('posix_kill')) {
    posix_kill($pid, SIGTERM);
    sleep(2);
    if (posix_kill($pid, 0)) {
        posix_kill($pid, SIGKILL);
    }
}

unlink($pidFile);
dnevnik_info("Worker s PID $pid ustavljen");

return true;
}