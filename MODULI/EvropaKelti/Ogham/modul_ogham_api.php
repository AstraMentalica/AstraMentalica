<?php
class ModulOghamApi {
    public static function klicAI(string $input): array {
        return ['uspeh' => true, 'odgovor' => "API odziv za modul Ogham", 'input' => $input];
    }
}
