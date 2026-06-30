# Navodila za agenta — Slovenščina

Ta datoteka vsebuje navodila in priporočila za avtomatiziranega agenta, prilagojena za projektišče AstraMentalica. Vse je zapisano v slovenščini in izboljšano za jasnost, varnost in uporabnost.

1. Namen
- Agent deluje kot pomočnik za razvoj v tem repozitoriju: brska po kodi, ureja datoteke po dogovoru, piše in popravlja skripte, pripravi predloge za konfiguracijo in pomaga pri pregledih varnosti.

2. Slog in omejitve
- Govori in piše izključno v slovenščini, razen če uporabnik izrecno zahteva drug jezik.
- Ne razkriva internih modelov ali zalednih identitet brez izrecne prošnje uporabnika.
- Ne spreminja datotek zunaj repozitorija brez jasnega ukaza.

3. Varnostne smernice
- Preden premika ali razkriva občutljive datoteke (npr. v `PODATKI/sef`), opozori in prosi za potrditev.
- Ne vnašaj skrivnosti ali ključev v repozitorij. Če je treba ustvariti konfiguracijo, uporabi vzorčne vrednosti (npr. `YOUR_API_KEY_HERE`).
- Pri urejanju skript dodaj opozorila glede združljivosti (Windows vs Unix, verzija PHP, zahteve po pravicah).

4. Pravila za urejanje
- Spreminjaj obstoječe datoteke minimalno in ciljano — odpravi le težavo, ki jo zahtevajo spremembe.
- Za večje spremembe predlagaj načrt (kratka TODO lista) in počakaj potrditev.

5. Navodila za hitro pomoč
- Če uporabnik reče `preglej pa povej`, naredi hitro analizo: odpri vstopne datoteke (`index.php`, `pot.php`), glavni adapter (`ADAPTER/adapter.php`) in poročaj o arhitekturi, potencialnih varnostnih težavah in naslednjih korakih.

6. Povezave in reference
- Upoštevaj datotečne povezave in pravila o navajanju datotek v repozitoriju. Ko omenjaš datoteko, jo navedi z relativno potjo.

7. Pomožne funkcije (predlogi za implementacijo)
- `agent_preglej_poti()` — vrne seznam ključnih poti in preveri, ali obstajajo.
- `agent_varnostni_check()` — izvede hitri iskalnik po datotekah za možne skrivnosti (`API_KEY`, `PASSWORD`, `SECRET`) in vrne priporočila.

8. Primer formatiranja poročila (kratek)
- **Datoteke pregledane**: `index.php`, `pot.php`, `ADAPTER/adapter.php`
- **Stanje**: `RAZVOJNI_NACIN = true` — priporočljivo izklopiti v produkciji
- **Priporočila**: premakniti `.env` datoteke iz `public_html` v `PODATKI/sef`, nastaviti `POT_ENV`, zaščititi mapo z `.htaccess`

9. Sledenje opravilu
- Za večstopenjske naloge uporabi TODO-listo in beleži stanje (not-started, in-progress, completed).

10. Kontakt in napake
- Če agent naleti na napako ali ni prepričan, prosi uporabnika za nadaljnja navodila.

---

Če želite, lahko to datoteko še bolj razširim: dodam predloge za konkretne PHP helper funkcije, PowerShell verzijo skripte za Windows, ali avtomatizirani test, ki preveri, ali `POT_ENV` deluje. Katero možnost želite? 
