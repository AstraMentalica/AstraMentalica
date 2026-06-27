<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/moduli/modul_varuh.php
 * v111 (02.06.2026)
 * ---------------------------------------------------------
 * OPIS: Sistem varuhov modulov – vsak modul ima svojega varuha
 * ---------------------------------------------------------
 */
declare(strict_types=1);

class VaruhModula
{
    private string $imeModula;
    private array $varuhPodatki;
    private static array $varuhi = [];

    public function __construct(string $imeModula)
    {
        $this->imeModula = $imeModula;
        $this->naloziVaruhe();
    }

    private function naloziVaruhe(): void
    {
        if (empty(self::$varuhi)) {
            $pot = POT_PODATKI_UPORABNIKI . '/moduli_varuhi.json';
            if (file_exists($pot)) {
                $data = json_decode(file_get_contents($pot), true);
                self::$varuhi = $data['varuhi'] ?? [];
            }
        }

        // Določi varuha za ta modul (glede na hash imena modula)
        $index = abs(crc32($this->imeModula)) % count(self::$varuhi);
        $this->varuhPodatki = self::$varuhi[$index] ?? self::$varuhi[0] ?? null;
    }

    public function pozdravi(): string
    {
        if (!$this->varuhPodatki) {
            return "Dobrodošel v modulu {$this->imeModula}.";
        }
        return $this->varuhPodatki['pozdrav'];
    }

    public function preveriDostop(int $stopnjaAvatarja, int $tockeAvatarja): array
    {
        if (!$this->varuhPodatki) {
            return ['dovoljeno' => true, 'sporocilo' => ''];
        }

        $zahtevanaStopnja = $this->pridobiZahtevanoStopnjo();

        if ($stopnjaAvatarja >= $zahtevanaStopnja) {
            return [
                'dovoljeno' => true,
                'sporocilo' => $this->varuhPodatki['odklenitev'] ?? 'Modul je odprt.'
            ];
        }

        return [
            'dovoljeno' => false,
            'sporocilo' => $this->varuhPodatki['zavrnitev'] ?? 'Nimaš dostopa do tega modula.',
            'potrebna_stopnja' => $zahtevanaStopnja,
            'trenutna_stopnja' => $stopnjaAvatarja
        ];
    }

    private function pridobiZahtevanoStopnjo(): int
    {
        // Zahtevana stopnja glede na modul (lahko iz manifesta)
        $manifest = modul_peskovnik_nalozi($this->imeModula)?->razglas() ?? [];
        return $manifest['zahtevana_stopnja_avatarja'] ?? 0;
    }

    public function pridobiOsebnost(): array
    {
        return [
            'ime' => $this->varuhPodatki['ime'] ?? 'Varuh',
            'osebnost' => $this->varuhPodatki['osebnost'] ?? 'moder',
            'ikona' => $this->varuhPodatki['ikona'] ?? '🛡️',
            'barva' => $this->varuhPodatki['barva'] ?? '#888'
        ];
    }

    public function posljiSporocilo(string $tip = 'pozdrav'): string
    {
        return match($tip) {
            'pozdrav' => $this->pozdravi(),
            'odklenitev' => $this->varuhPodatki['odklenitev'] ?? '',
            'zavrnitev' => $this->varuhPodatki['zavrnitev'] ?? '',
            default => ''
        };
    }
}

// Globalne funkcije
function varuh_modula(string $imeModula): VaruhModula
{
    return new VaruhModula($imeModula);
}

function modul_preveri_dostop_z_varuhom(string $imeModula, string $uporabnikId): array
{
    $avatar = avatar_pridobi($uporabnikId);
    $podatki = $avatar->pridobiPodatke();

    $varuh = varuh_modula($imeModula);
    return $varuh->preveriDostop($podatki['stopnja'], $podatki['tocke']);
}