<?php
class ModulDevorumApi {
    public static function klicAI(string $input): array {
        return ['uspeh' => true, 'odgovor' => "API odziv za modul Devorum", 'input' => $input];
    }
}
