<?php
/**
 * Razred za delo s podatkovno bazo
 * Osnovne operacije za povezavo in poizvedbe
 */
namespace Stelaris\Jedro;

class PodatkovnaBaza {
    private \PDO $pdo;
    
    public function __construct() {
        $n = require __DIR__ . '/../nastavitve/baza_podatkov.php';
        $this->pdo = new \PDO(
            "mysql:host={$n['gostitelj']};dbname={$n['ime_baze']};charset={$n['kodiranje']}",
            $n['uporabnisko_ime'],
            $n['geslo'],
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );
    }
    
    public function vstavi(string $tabela, array $podatki): int {
        $stolpci = implode(', ', array_keys($podatki));
        $oznake = ':' . implode(', :', array_keys($podatki));
        $stmt = $this->pdo->prepare("INSERT INTO $tabela ($stolpci) VALUES ($oznake)");
        $stmt->execute($podatki);
        return (int)$this->pdo->lastInsertId();
    }
    
    public function poisci(string $tabela, array $pogoji = []): array {
        $sql = "SELECT * FROM $tabela";
        if ($pogoji) {
            $sql .= " WHERE " . implode(' AND ', array_map(fn($k) => "$k = :$k", array_keys($pogoji)));
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($pogoji);
        return $stmt->fetchAll();
    }
}