<?php
// cron_izvajalec.php
// Datoteka, ki se kliče preko cron joba

require_once 'blog_avtomatizacija.php';
require_once 'ai_komunikacija.php';
require_once 'cron_sistem.php';

// Zaženi cron sistem
CronIzvajalec::zaženi();
?>