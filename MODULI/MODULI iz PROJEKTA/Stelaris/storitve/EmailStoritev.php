<?php
/**
 * Storitev za pošiljanje e-poštnih obvestil
 * Push obvestila za pomembne tranzite
 */
namespace Stelaris\Storitve;

class EmailStoritev {
    public function posljiObvestiloOTranzitu(string $email, array $tranzit): bool {
        $zadeva = "Pomemben astrološki tranzit - {$tranzit['planet']}";
        $sporocilo = $this->pripraviSporociloOTranzitu($tranzit);
        
        return mail($email, $zadeva, $sporocilo);
    }
    
    private function pripraviSporociloOTranzitu(array $tranzit): string {
        return "Pozdravljeni!\n\nPomemben astrološki tranzit: {$tranzit['planet']} 
                v znamenju {$tranzit['znamenje']}.\n\nVpliv: {$tranzit['vpliv']}\n\nLep pozdrav,\nEkipa Stelaris";
    }
}