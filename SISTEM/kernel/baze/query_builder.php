<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/kernel/baze/query_builder.php
 * v111 (27.5.2026 14:30)
 * ---------------------------------------------------------
 * OPIS: Query builder – gradnja SQL poizvedb
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - pot.php
 *
 * UPORABA:
 * - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * FUNKCIJE:
 * - QueryBuilder – gradnja SELECT, INSERT, UPDATE, DELETE
 *
 * PREPOVEDI:
 * - Brez echo, print_r, var_dump
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 38 – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

class QueryBuilder
{
private string $tabela;
private array $izbrani = ['*'];
private array $pogoji = [];
private array $parametri = [];
private array $join = [];
private array $groupBy = [];
private array $orderBy = [];
private ?int $limit = null;
private ?int $offset = null;

public function __construct(string $tabela)
{
    $this->tabela = $tabela;
}

public static function tabela(string $tabela): self
{
    return new self($tabela);
}

public function izberi(...$stolpci): self
{
    if (empty($stolpci)) {
        $this->izbrani = ['*'];
    } else {
        $this->izbrani = $stolpci;
    }
    return $this;
}

public function kje(string $pogoj, array $parametri = []): self
{
    $this->pogoji[] = $pogoj;
    $this->parametri = array_merge($this->parametri, $parametri);
    return $this;
}

public function in_kje(string $pogoj, array $parametri = []): self
{
    if (!empty($this->pogoji)) {
        $this->pogoji[] = "AND ($pogoj)";
    } else {
        $this->pogoji[] = $pogoj;
    }
    $this->parametri = array_merge($this->parametri, $parametri);
    return $this;
}

public function ali_kje(string $pogoj, array $parametri = []): self
{
    if (!empty($this->pogoji)) {
        $this->pogoji[] = "OR ($pogoj)";
    } else {
        $this->pogoji[] = $pogoj;
    }
    $this->parametri = array_merge($this->parametri, $parametri);
    return $this;
}

public function zdruzi(string $tabela, string $pogoj, string $tip = 'INNER'): self
{
    $this->join[] = "$tip JOIN $tabela ON $pogoj";
    return $this;
}

public function levi_zdruzi(string $tabela, string $pogoj): self
{
    return $this->zdruzi($tabela, $pogoj, 'LEFT');
}

public function desni_zdruzi(string $tabela, string $pogoj): self
{
    return $this->zdruzi($tabela, $pogoj, 'RIGHT');
}

public function grupiraj(...$stolpci): self
{
    $this->groupBy = $stolpci;
    return $this;
}

public function uredi(string $stolpec, string $smer = 'ASC'): self
{
    $this->orderBy[] = "$stolpec $smer";
    return $this;
}

public function omeji(int $limit, ?int $offset = null): self
{
    $this->limit = $limit;
    if ($offset !== null) {
        $this->offset = $offset;
    }
    return $this;
}

public function odmik(int $offset): self
{
    $this->offset = $offset;
    return $this;
}

public function zgradiSelect(): string
{
    $sql = "SELECT " . implode(', ', $this->izbrani);
    $sql .= " FROM " . $this->tabela;
    
    if (!empty($this->join)) {
        $sql .= " " . implode(' ', $this->join);
    }
    
    if (!empty($this->pogoji)) {
        $sql .= " WHERE " . implode(' ', $this->pogoji);
    }
    
    if (!empty($this->groupBy)) {
        $sql .= " GROUP BY " . implode(', ', $this->groupBy);
    }
    
    if (!empty($this->orderBy)) {
        $sql .= " ORDER BY " . implode(', ', $this->orderBy);
    }
    
    if ($this->limit !== null) {
        $sql .= " LIMIT " . $this->limit;
        if ($this->offset !== null) {
            $sql .= " OFFSET " . $this->offset;
        }
    }
    
    return $sql;
}

public function zgradiInsert(array $podatki): string
{
    $stolpci = implode(', ', array_keys($podatki));
    $vrednosti = ':' . implode(', :', array_keys($podatki));
    
    return "INSERT INTO " . $this->tabela . " ($stolpci) VALUES ($vrednosti)";
}

public function zgradiUpdate(array $podatki): string
{
    $set = [];
    foreach (array_keys($podatki) as $kljuc) {
        $set[] = "$kljuc = :$kljuc";
    }
    
    $sql = "UPDATE " . $this->tabela . " SET " . implode(', ', $set);
    
    if (!empty($this->pogoji)) {
        $sql .= " WHERE " . implode(' ', $this->pogoji);
    }
    
    return $sql;
}

public function zgradiDelete(): string
{
    $sql = "DELETE FROM " . $this->tabela;
    
    if (!empty($this->pogoji)) {
        $sql .= " WHERE " . implode(' ', $this->pogoji);
    }
    
    return $sql;
}

public function parametri(): array
{
    return $this->parametri;
}

public function ponastavi(): void
{
    $this->izbrani = ['*'];
    $this->pogoji = [];
    $this->parametri = [];
    $this->join = [];
    $this->groupBy = [];
    $this->orderBy = [];
    $this->limit = null;
    $this->offset = null;
}
}

// Globalne funkcije
function query_builder(string $tabela): QueryBuilder
{
return new QueryBuilder($tabela);
}