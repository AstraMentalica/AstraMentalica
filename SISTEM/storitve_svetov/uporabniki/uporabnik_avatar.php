<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/storitve_svetov/uporabniki/avatar.php
 * v111 (02.06.2026)
 * ---------------------------------------------------------
 * OPIS: Upravljanje avatarja – točke, stopnje, arhetip
 * ---------------------------------------------------------
 */
declare(strict_types=1);

class Avatar
{
    private string $uporabnikId;
    private array $podatki;
    private array $arhetipi;
    private array $stopnje;

    public function __construct(string $uporabnikId)
    {
        $this->uporabnikId = $uporabnikId;
        $this->naloziPodatke();
        $this->naloziArhetipe();
    }

    private function naloziPodatke(): void
    {
        $pot = POT_PODATKI_UPORABNIKI . '/' . $this->uporabnikId . '/avatar.json';
        if (file_exists($pot)) {
            $this->podatki = json_decode(file_get_contents($pot), true) ?? [];
        }

        // Privzeti podatki za novega uporabnika
        if (empty($this->podatki)) {
            $this->podatki = [
                'stopnja' => 0,
                'tocke' => 0,
                'arhetip' => null,
                'zgodovina_tock' => [],
                'zadnja_posodobitev' => time(),
                'ime' => 'Meglica',
                'ikona' => '🌫️'
            ];
            $this->shraniPodatke();
        }
    }

    private function naloziArhetipe(): void
    {
        $pot = POT_PODATKI_UPORABNIKI . '/avatar_arhetipi.json';
        if (file_exists($pot)) {
            $data = json_decode(file_get_contents($pot), true);
            $this->arhetipi = $data['arhetipi'] ?? [];
            $this->stopnje = $data['zacetni']['stopnje'] ?? [];
        } else {
            $this->arhetipi = [];
            $this->stopnje = [];
        }
    }

    private function shraniPodatke(): void
    {
        $pot = POT_PODATKI_UPORABNIKI . '/' . $this->uporabnikId;
        if (!is_dir($pot)) {
            mkdir($pot, 0755, true);
        }
        file_put_contents($pot . '/avatar.json', json_encode($this->podatki, JSON_PRETTY_PRINT));
    }

    public function dodajTocke(int $tocke, string $razlog): void
    {
        $this->podatki['tocke'] += $tocke;
        $this->podatki['zgodovina_tock'][] = [
            'tocke' => $tocke,
            'razlog' => $razlog,
            'cas' => time()
        ];

        // Omeji zgodovino na 1000 zapisov
        if (count($this->podatki['zgodovina_tock']) > 1000) {
            array_shift($this->podatki['zgodovina_tock']);
        }

        $this->posodobiStopnjo();
        $this->posodobiArhetip();
        $this->shraniPodatke();
    }

    private function posodobiStopnjo(): void
    {
        $novaStopnja = 0;
        foreach ($this->stopnje as $stopnja) {
            if ($this->podatki['tocke'] >= $stopnja['zahteva_tocke']) {
                $novaStopnja = $stopnja['stopnja'];
                $this->podatki['ime'] = $stopnja['ime'];
                $this->podatki['ikona'] = $stopnja['ikona'];
            }
        }

        if ($novaStopnja != $this->podatki['stopnja']) {
            $this->podatki['stopnja'] = $novaStopnja;
            $this->podatki['zadnja_posodobitev'] = time();

            // Sproži dogodek za dosežek
            dogodek_sprozi('avatar.stopnja', [
                'uporabnik_id' => $this->uporabnikId,
                'nova_stopnja' => $novaStopnja,
                'ime' => $this->podatki['ime']
            ]);
        }
    }

    private function posodobiArhetip(): void
    {
        if (empty($this->arhetipi)) return;

        // Preberi statistiko uporabnika
        $statistika = uporabnik_statistika_pridobi($this->uporabnikId);
        $arhetipTocke = [];

        foreach ($this->arhetipi as $arhetip) {
            $tocke = 0;
            foreach ($arhetip['pridobi_tocke'] as $tip) {
                $tocke += $statistika[$tip] ?? 0;
            }
            $arhetipTocke[$arhetip['id']] = $tocke;
        }

        // Izberi arhetip z največ točkami
        $najboljsi = null;
        $najvecTock = 0;
        foreach ($arhetipTocke as $id => $tocke) {
            if ($tocke > $najvecTock) {
                $najvecTock = $tocke;
                $najboljsi = $id;
            }
        }

        if ($najboljsi && $najboljsi !== $this->podatki['arhetip']) {
            $this->podatki['arhetip'] = $najboljsi;
            $this->podatki['zadnja_posodobitev'] = time();

            dogodek_sprozi('avatar.arhetip', [
                'uporabnik_id' => $this->uporabnikId,
                'nov_arhetip' => $najboljsi
            ]);
        }
    }

    public function pridobiPodatke(): array
    {
        $arhetipPodatki = null;
        if ($this->podatki['arhetip']) {
            foreach ($this->arhetipi as $a) {
                if ($a['id'] === $this->podatki['arhetip']) {
                    $arhetipPodatki = $a;
                    break;
                }
            }
        }

        return [
            'stopnja' => $this->podatki['stopnja'],
            'tocke' => $this->podatki['tocke'],
            'ime' => $this->podatki['ime'],
            'ikona' => $this->podatki['ikona'],
            'arhetip' => $arhetipPodatki,
            'naslednja_stopnja' => $this->pridobiNaslednjoStopnjo(),
            'odstotek_do_naslednje' => $this->pridobiOdstotekDoNaslednje()
        ];
    }

    private function pridobiNaslednjoStopnjo(): ?array
    {
        foreach ($this->stopnje as $stopnja) {
            if ($stopnja['stopnja'] > $this->podatki['stopnja']) {
                return $stopnja;
            }
        }
        return null;
    }

    private function pridobiOdstotekDoNaslednje(): int
    {
        $naslednja = $this->pridobiNaslednjoStopnjo();
        if (!$naslednja) return 100;

        $trenutnaTocke = $this->podatki['tocke'];
        $trenutnaMeja = 0;
        foreach ($this->stopnje as $stopnja) {
            if ($stopnja['stopnja'] === $this->podatki['stopnja']) {
                $trenutnaMeja = $stopnja['zahteva_tocke'];
                break;
            }
        }

        $potrebno = $naslednja['zahteva_tocke'] - $trenutnaMeja;
        $ima = $trenutnaTocke - $trenutnaMeja;

        if ($potrebno <= 0) return 100;
        return min(100, (int)($ima / $potrebno * 100));
    }
}

// Globalne funkcije
function avatar_pridobi(string $uporabnikId): Avatar
{
    return new Avatar($uporabnikId);
}

function avatar_dodaj_tocke(string $uporabnikId, int $tocke, string $razlog): void
{
    $avatar = new Avatar($uporabnikId);
    $avatar->dodajTocke($tocke, $razlog);
}