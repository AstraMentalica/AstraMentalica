<?php
/**
 * ============================================================
 * POT: ADAPTER/zahteve/adapter_zahteva.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER
 *
 * 📰 NAMEN:
 *     Objekt za normalizirano zahtevo.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - id(): string
 *     - pot(): string
 *     - metoda(): string
 *     - parametri(): array
 *     - vsebina(): ?array
 *     - glave(): array
 *     - casPrejema(): int
 *     - ip(): string
 *     - kanal(): ?string
 *     - nastaviKanal(string $kanal): void
 *     - pridobiParam(string $kljuc, $privzeto = null)
 *     - imaParam(string $kljuc): bool
 *
 * 📡 ODVISNOSTI:
 *     - (nobene)
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *     - Brez direktnih poti (uporabi konstante!)
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v114: uskladitev s Header Standard v114
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, zahteve
 * ============================================================
 */
declare(strict_types=1);

class AdapterZahteva
{
    private string $id;
    private string $pot;
    private string $metoda;
    private array $parametri;
    private ?array $vsebina;
    private array $glave;
    private int $casPrejema;
    private string $ip;
    private ?string $kanal;
    
    public function __construct(array $podatki = [])
    {
        $this->id = $podatki['id_zahteve'] ?? uniqid('req_', true);
        $this->pot = $podatki['pot'] ?? 'GLOBALNO';
        $this->metoda = $podatki['metoda'] ?? 'DOBI';
        $this->parametri = $podatki['parametri'] ?? [];
        $this->vsebina = $podatki['vsebina'] ?? null;
        $this->glave = $podatki['glave'] ?? [];
        $this->casPrejema = $podatki['cas_prejema'] ?? time();
        $this->ip = $podatki['ip'] ?? '';
        $this->kanal = $podatki['kanal'] ?? null;
    }
    
    public function id(): string
    {
        return $this->id;
    }
    
    public function pot(): string
    {
        return $this->pot;
    }
    
    public function metoda(): string
    {
        return $this->metoda;
    }
    
    public function parametri(): array
    {
        return $this->parametri;
    }
    
    public function vsebina(): ?array
    {
        return $this->vsebina;
    }
    
    public function glave(): array
    {
        return $this->glave;
    }
    
    public function casPrejema(): int
    {
        return $this->casPrejema;
    }
    
    public function ip(): string
    {
        return $this->ip;
    }
    
    public function kanal(): ?string
    {
        return $this->kanal;
    }
    
    public function nastaviKanal(string $kanal): void
    {
        $this->kanal = $kanal;
    }
    
    public function pridobiParam(string $kljuc, $privzeto = null)
    {
        return $this->parametri[$kljuc] ?? $this->vsebina[$kljuc] ?? $privzeto;
    }
    
    public function imaParam(string $kljuc): bool
    {
        return isset($this->parametri[$kljuc]) || isset($this->vsebina[$kljuc]);
    }
    
    public function toArray(): array
    {
        return [
            'id_zahteve' => $this->id,
            'pot' => $this->pot,
            'metoda' => $this->metoda,
            'parametri' => $this->parametri,
            'vsebina' => $this->vsebina,
            'glave' => $this->glave,
            'cas_prejema' => $this->casPrejema,
            'ip' => $this->ip,
            'kanal' => $this->kanal
        ];
    }
}