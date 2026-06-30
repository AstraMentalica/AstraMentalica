<?php
class ModulCodexApi {
    public static function klicAI(string $input): array {
        return ['uspeh' => true, 'odgovor' => "API odziv za modul Codex", 'input' => $input];
    }
}
