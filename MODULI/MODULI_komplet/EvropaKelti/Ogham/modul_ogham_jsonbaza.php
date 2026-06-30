<?php
class ModulOghamJsonBaza {
    private array $data;
    public function __construct() { $p = __DIR__ . "/modul.json"; $this->data = file_exists($p) ? (json_decode(file_get_contents($p), true) ?? []) : []; }
    public function pridobiVse(): array { return $this->data; }
    public function shraniVse(array $d): void { file_put_contents(__DIR__ . "/modul.json", json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); }
}
