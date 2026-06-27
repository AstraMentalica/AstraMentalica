"""
env_loader.py
Pametno nalaganje več .env datotek – vsak modul ima svojo.
Prioriteta: .env_local > .env_{modul} > .env
"""
import os
from pathlib import Path


def nalozi_vse_env(pot: str | Path = ".") -> dict:
    """
    Naloži vse .env datoteke v mapi po prioriteti.
    Vrne slovar vseh naloženih ključev.
    """
    pot = Path(pot)
    naloženo = {}

    # 1. Osnovna .env (najmanjša prioriteta)
    _beri_env(pot / ".env", naloženo)

    # 2. Vse .env_* datoteke (abecedno)
    for datoteka in sorted(pot.glob(".env_*")):
        if datoteka.is_file():
            _beri_env(datoteka, naloženo)
            print(f"  ✅ Naložena: {datoteka.name}")

    # 3. .env_local (najvišja prioriteta – prepiše vse)
    if (pot / ".env_local").exists():
        _beri_env(pot / ".env_local", naloženo)
        print(f"  🔒 Lokalni override: .env_local")

    # Nastavi v okolje
    for k, v in naloženo.items():
        os.environ.setdefault(k, v)

    return naloženo


def _beri_env(pot: Path, slovar: dict) -> None:
    """Preberi .env datoteko in dodaj v slovar."""
    if not pot.exists():
        return
    for vrstica in pot.read_text(encoding="utf-8").splitlines():
        vrstica = vrstica.strip()
        if not vrstica or vrstica.startswith("#"):
            continue
        if "=" not in vrstica:
            continue
        kljuc, _, vrednost = vrstica.partition("=")
        kljuc = kljuc.strip()
        vrednost = vrednost.strip().strip('"').strip("'")
        slovar[kljuc] = vrednost


def pridobi_api_kljuc(modul: str, env_mapa: str | Path = ".") -> str:
    """
    Pametno poišči API ključ za konkreten modul.
    
    Vrstni red iskanja:
    1. ANTHROPIC_API_KEY_{MODUL} (npr. ANTHROPIC_API_KEY_VARUH)
    2. {MODUL}_API_KEY (npr. VARUH_API_KEY)
    3. ANTHROPIC_API_KEY (splošni)
    """
    nalozi_vse_env(env_mapa)
    
    modul_u = modul.upper().replace("-", "_")
    
    kandidati = [
        f"ANTHROPIC_API_KEY_{modul_u}",
        f"{modul_u}_API_KEY",
        f"{modul_u}_ANTHROPIC_KEY",
        "ANTHROPIC_API_KEY",
    ]
    
    for k in kandidati:
        v = os.environ.get(k, "")
        if v and v.startswith("sk-"):
            print(f"  🔑 API ključ za '{modul}': {k} ✓")
            return v
    
    print(f"  ⚠️  Ni API ključa za modul '{modul}'")
    return ""


def pridobi_model(modul: str, privzeto: str = "claude-opus-4-6") -> str:
    """Poišči model za konkreten modul."""
    modul_u = modul.upper().replace("-", "_")
    return (
        os.environ.get(f"{modul_u}_MODEL")
        or os.environ.get("VARUH_MODEL")
        or os.environ.get("ANTHROPIC_MODEL")
        or privzeto
    )


# ── Primer .env datotek ───────────────────────────────────────────────────────
PRIMER_ENV_VARUH = """# .env_varuh – ključi za sistem Duhovnih Varuhov
ANTHROPIC_API_KEY_VARUH=sk-ant-xxxx
VARUH_MODEL=claude-opus-4-6
PORT=5757
ADMIN_KEY=tvoj_admin_geslo
"""

PRIMER_ENV_AVATAR = """# .env_avatar – ključi za Avatar sistem
ANTHROPIC_API_KEY_AVATAR=sk-ant-yyyy
AVATAR_MODEL=claude-sonnet-4-6
"""

PRIMER_ENV_KNJIZNICA = """# .env_knjiznica – ključi za Knjižnico
ANTHROPIC_API_KEY_KNJIZNICA=sk-ant-zzzz
KNJIZNICA_MODEL=claude-haiku-4-5-20251001
"""

PRIMER_ENV_TRZNICA = """# .env_trznica – ključi za Tržnico
ANTHROPIC_API_KEY_TRZNICA=sk-ant-wwww
TRZNICA_MODEL=claude-haiku-4-5-20251001
"""

PRIMER_ENV_SPLOSNI = """# .env – splošni (rezerva za vse module)
ANTHROPIC_API_KEY=sk-ant-aaaa
"""


def ustvari_primere_env(pot: str | Path = ".") -> None:
    """Ustvari vzorčne .env datoteke če ne obstajajo."""
    pot = Path(pot)
    primeri = {
        ".env": PRIMER_ENV_SPLOSNI,
        ".env_varuh": PRIMER_ENV_VARUH,
        ".env_avatar": PRIMER_ENV_AVATAR,
        ".env_knjiznica": PRIMER_ENV_KNJIZNICA,
        ".env_trznica": PRIMER_ENV_TRZNICA,
    }
    for ime, vsebina in primeri.items():
        p = pot / ime
        if not p.exists():
            p.write_text(vsebina, encoding="utf-8")
            print(f"  📄 Ustvarjena: {ime}")
        else:
            print(f"  ⏭️  Obstaja: {ime}")


if __name__ == "__main__":
    print("\n🔧 ENV Loader – test\n")
    print("Ustvarjam vzorčne datoteke...")
    ustvari_primere_env(".")
    print("\nNalagam vse .env datoteke...")
    vse = nalozi_vse_env(".")
    print(f"\nNaloženih {len(vse)} spremenljivk.")
    print("\nTest pridobivanja ključev:")
    for modul in ["varuh", "avatar", "knjiznica", "trznica"]:
        kljuc = pridobi_api_kljuc(modul)
        model = pridobi_model(modul)
        print(f"  {modul:12s} → ključ: {'✓' if kljuc else '✗'}, model: {model}")
