<?php
/**
 * Generator AI vsebin za astrologijo
 * Samostojno pisanje analiz, razlag in člankov
 */
namespace Stelaris\Modeli;

class GeneratorAI {
    public function generirajDnevniHoroskop(string $znamenje): string {
        $horoskopi = [
            'Oven' => "Danes je cas za odlocne korake. Mars v vasem znamenju vam daje pogum za nove izzive. Izkoristite to energijo za zacetek pomembnih projektov.",
            'Bik' => "Venerin vpliv prinaša harmonijo v odnose. Dan je idealen za krepitev vezi in uživanje v lepih trenutkih. Posvetite cas ljubljenim osebam.",
            'Rak' => "Luna vam prinaša emocionalno obcutljivost. Posvetite cas samo sebi in svojemu notranjemu svetu. Vecina odlocitev je bolje jih pustiti za drug dan.",
            'Lev' => "Sonce osvetljuje vašo kreativno stran. Danes boste se posebno privlacni in karizmaticni. Izkoristite to za predstavitev svojih idej.",
            'Devica' => "Merkur vam daje analiticnost. Resite organizacijske zadeve in uredite vsakodnevne obveznosti. Pozornost na podrobnosti vam bo prinesla uspeh.",
            'Tehtnica' => "Venera prinaša ravnotežje v odnose. Resite morebitne konflikte z diplomacijo. Sodelovanje z drugimi vam bo danes še posebej uspelo.",
            'Skorpijon' => "Pluton vam daje globino v razmisljanju. Poglobite se v skrite resnice in se posvetite osebni preobrazbi. Zaupajte svojemu nagonu.",
            'Strelec' => "Jupiter razsirja vaše obzorja. Dan je idealen za ucenje novih stvari in načrtovanje potovanj. Iskanje resnice vam bo danes šlo od rok.",
            'Kozorog' => "Saturn vam daje disciplino. Osredotocite se na dolgorocne cilje in karierno napredovanje. Vsa truda se vam bosta obrestovala.",
            'Vodnar' => "Uran prinaša inovativne ideje. Presenetite okolje s svojimi izvirnimi resitvami. Srečujte se z novimi ljudmi za navdih.",
            'Ribe' => "Neptun okrepuje vašo intuitivnost. Zaupajte svojim cutem in sanjam. Umetnost in spiritualnost vam bosta danes v pomoc."
        ];
        
        return $horoskopi[$znamenje] ?? "Danes je dan za pozitivne spremembe. Odprite se novim priložnostim in zaupajte vesolju.";
    }
    
    public function generirajNatalnoAnalizo(array $natalnaKarta): string {
        return "Vaša natalna karta kaže na {$natalnaKarta['sončno_znamenje']} znamenje z mocnimi vplivi planetov. 
                Analiza razkriva vaše prednosti in izzive za osebno rast.";
    }
}