-- MODULI/osnovni/Aeternum/.baza/shema.sql

-- Tabela za vsebino
CREATE TABLE IF NOT EXISTS vsebina (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    naslov TEXT NOT NULL,
    vsebina TEXT NOT NULL,
    povzetek TEXT,
    kategorija TEXT NOT NULL,
    podkategorija TEXT,
    avtor TEXT DEFAULT 'AI',
    tags TEXT,
    jezik TEXT DEFAULT 'sl',
    starostna_omejitev INTEGER DEFAULT 0,
    stil TEXT DEFAULT 'modernus',      -- antiquus, modernus, puerilis
    globina INTEGER DEFAULT 1,          -- 1-10 (1=lahko, 10=hermetično)
    starost_priporocilo INTEGER DEFAULT 7, -- 3, 5, 7, 9, 12
    slika_pogled TEXT,                  -- URL do ilustracije
    barva_tema TEXT DEFAULT 'zlata',    -- zlata, modra, zelena, roza
    verzija INTEGER DEFAULT 1,
    ogledi INTEGER DEFAULT 0,
    ustvarjeno TEXT NOT NULL,
    posodobljeno TEXT NOT NULL
);

-- Tabela za iskanje (full-text)
CREATE VIRTUAL TABLE IF NOT EXISTS vsebina_fts USING fts5(
    naslov,
    vsebina,
    povzetek,
    content=vsebina
);

-- Trigger za sinhronizacijo FTS
CREATE TRIGGER IF NOT EXISTS vsebina_after_insert AFTER INSERT ON vsebina BEGIN
    INSERT INTO vsebina_fts(rowid, naslov, vsebina, povzetek)
    VALUES (new.id, new.naslov, new.vsebina, new.povzetek);
END;

CREATE TRIGGER IF NOT EXISTS vsebina_after_update AFTER UPDATE ON vsebina BEGIN
    UPDATE vsebina_fts 
    SET naslov = new.naslov, vsebina = new.vsebina, povzetek = new.povzetek
    WHERE rowid = new.id;
END;

CREATE TRIGGER IF NOT EXISTS vsebina_after_delete AFTER DELETE ON vsebina BEGIN
    DELETE FROM vsebina_fts WHERE rowid = old.id;
END;

-- Tabela za zapiske uporabnikov
CREATE TABLE IF NOT EXISTS uporabniski_zapiski (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    uporabnik_id INTEGER NOT NULL,
    vsebina_id INTEGER NOT NULL,
    zapisek TEXT,
    stran INTEGER DEFAULT 0,
    ustvarjeno TEXT NOT NULL,
    FOREIGN KEY (vsebina_id) REFERENCES vsebina(id) ON DELETE CASCADE
);

-- Tabela za glasovne dnevnike
CREATE TABLE IF NOT EXISTS glasovni_dnevniki (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    uporabnik_id INTEGER NOT NULL,
    posnetek TEXT,                  -- pot do audio datoteke
    prepis TEXT,
    trajanje INTEGER,               -- v sekundah
    ustvarjeno TEXT NOT NULL
);

-- Indeksi
CREATE INDEX IF NOT EXISTS idx_vsebina_kategorija ON vsebina(kategorija);
CREATE INDEX IF NOT EXISTS idx_vsebina_ustvarjeno ON vsebina(ustvarjeno);
CREATE INDEX IF NOT EXISTS idx_zapiski_uporabnik ON uporabniski_zapiski(uporabnik_id);
CREATE INDEX IF NOT EXISTS idx_zapiski_vsebina ON uporabniski_zapiski(vsebina_id);