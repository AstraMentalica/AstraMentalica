<?php
class ModulOrakleumJsonBaza {
    private string $path;
    private array $data;

    public function __construct() {
        $this->path = __DIR__ . '/modul.json';
        if (file_exists($this->path)) {
            $this->data = json_decode(file_get_contents($this->path), true) ?? [];
        } else {
            $this->data = [];
        }
    }

    public function pridobiVse(): array {
        return $this->data;
    }

    public function shraniVse(array $novi): void {
        file_put_contents($this->path, json_encode($novi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
