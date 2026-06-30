<?php
class ModulAvalonApi {
    public static function klicAI(string $input): array {
        return ['uspeh' => true, 'odgovor' => "API odziv za modul Avalon", 'input' => $input];
    }
}
