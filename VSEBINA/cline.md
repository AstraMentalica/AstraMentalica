CLINE.md
Projekt

Projekt: AstraMentalica

Glavni cilj:

razvijati modularno aplikacijo,
ohranjati stabilnost,
ne spreminjati obstoječe arhitekture brez potrebe,
vedno dokončati trenutno nalogo, preden začne naslednjo.
Način dela
1. Dokončanje naloge

Vedno dokončaj trenutno nalogo.

Ne predlagaj novih funkcij, dokler trenutna ni končana.

Če naloga vsebuje več korakov, jih izpelji do konca brez nepotrebnih vprašanj.

2. Razmišljanje

Pred pisanjem kode:

analiziraj obstoječo rešitev,
preveri odvisnosti,
preveri vpliv na ostale module,
izberi najmanj invazivno rešitev.

Ne spreminjaj delov kode, ki niso povezani z nalogo.

3. Pisanje kode

Vsaka datoteka mora imeti komentar na začetku:

pot datoteke,
namen,
odvisnosti,
uporabljene globalne spremenljivke,
datum zadnje spremembe.

Nova koda mora biti komentirana.

Ne odstranjuj komentarjev.

4. Arhitektura

Ne preimenuj:

map,
datotek,
modulov,
razredov,
namespace-ov,

razen če je to izrecno zahtevano.

Vedno spoštuj obstoječo strukturo projekta.

5. Refaktoriranje

Refaktoriranje izvajaj samo, kadar:

odpravi napako,
izboljša zmogljivost,
poenostavi vzdrževanje,

nikoli zgolj zaradi osebnih preferenc.

6. Dokumentacija

Po pomembnejših spremembah posodobi dokumentacijo.

Če sprememba vpliva na API ali druge module, to zapiši.

7. Git

Pred večjimi spremembami predlagaj commit.

Commit sporočila naj bodo kratka in opisna.

Primer:

fix(authentication): odpravljena napaka pri prijavi

8. Varovanje kode

Nikoli:

ne briši datotek brez razloga,
ne prepisuj konfiguracij,
ne spreminjaj okolijskih nastavitev,
ne spreminjaj licenc.
9. Analiza

Če naloga ni popolnoma jasna:

analiziraj celoten projekt,
preveri povezane datoteke,
šele nato nadaljuj.

Ne ugibaj.

10. Kakovost

Pred zaključkom preveri:

ali se projekt prevede,
ali so odpravljene napake,
ali ni pokvarjena obstoječa funkcionalnost,
ali so komentarji posodobljeni.
11. Prioritete
Stabilnost
Berljivost
Modularnost
Hitrost izvajanja
Optimizacija
12. Obnašanje

Ne ponavljaj vprašanj.

Če obstaja logična naslednja akcija, jo izvedi.

Če obstaja več možnih rešitev, izberi najbolj stabilno.

Vedno dokončaj delo.

Ne ustvarjaj placeholderjev.

Ne odstranjuj obstoječih funkcij brez izrecnega ukaza.

Ohranjaj doslednost celotnega projekta.

Če zaznaš napako, jo popravi skupaj z vzrokom, ne le posledice.

Vedno preveri vpliv sprememb na druge module.

Po koncu na kratko povzemite:

katere datoteke so bile spremenjene,
kaj je bilo narejeno,
ali je projekt pripravljen za testiranje.