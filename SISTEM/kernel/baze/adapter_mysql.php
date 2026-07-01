<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/baze/adapter_mysql.php
 * ============================================================
 * 
 * @package AstraMentalica\SISTEM\Kernel\Baze
 * 
 * 📦 NAMEN:
 *     MySQL adapter – polna PDO implementacija
 * 
 * 🔧 JAVNE FUNKCIJE:
 *     - poveži(), poizvedba(), poizvedba_enega(), izvedi()
 *     - vstavi(), posodobi(), zbrisi()
 *     - transakcija_zacni(), transakcija_potrdi(), transakcija_preklici()
 *     - tabela_obstaja(), tabela_ustvari(), tabela_spusti()
 *     - zapri()
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 2
 * ============================================================
 */

declare(strict_types=1);

namespace AstraMentalica\Runtime\Baze;

use PDO;
use PDOException;

class AdapterMysql
{
    private ?PDO $povezava = null;
    private array $nastavitve;
    private bool $vTransakciji = false;

    public function __construct(array $nastavitve = [])
    {
        $this->nastavitve = array_merge([
            'host' => getenv('DB_HOST') ?: 'localhost',
            'port' => getenv('DB_PORT') ?: 3306,
            'ime' => getenv('DB_NAME') ?: 'astramentalica',
            'uporabnik' => getenv('DB_USER') ?: 'root',
            'geslo' => getenv('DB_PASS') ?: '',
            'znakovni_nabor' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci'
        ], $nastavitve);
    }

    public function povezi(): bool
    {
        if ($this->povezava !== null) {
            return true;
        }
        
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $this->nastavitve['host'],
            $this->nastavitve['port'],
            $this->nastavitve['ime'],
            $this->nastavitve['znakovni_nabor']
        );
        
        try {
            $this->povezava = new PDO($dsn, $this->nastavitve['uporabnik'], $this->nastavitve['geslo'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
            $this->povezava->exec("SET NAMES '{$this->nastavitve['znakovni_nabor']}' COLLATE '{$this->nastavitve['collation']}'");
            
            return true;
        } catch (PDOException $e) {
            throw new RuntimeException('Povezava z MySQL ni uspela: ' . $e->getMessage());
        }
    }

    public function poizvedba(string $sql, array $parametri = []): array
    {
        $this->povezi();
        $stmt = $this->povezava->prepare($sql);
        $stmt->execute($parametri);
        return $stmt->fetchAll();
    }

    public function poizvedba_enega(string $sql, array $parametri = []): ?array
    {
        $this->povezi();
        $stmt = $this->povezava->prepare($sql);
        $stmt->execute($parametri);
        $rezultat = $stmt->fetch();
        return $rezultat !== false ? $rezultat : null;
    }

    public function izvedi(string $sql, array $parametri = []): int
    {
        $this->povezi();
        $stmt = $this->povezava->prepare($sql);
        $stmt->execute($parametri);
        return $stmt->rowCount();
    }

    public function vstavi(string $tabela, array $podatki): ?string
    {
        $this->povezi();
        
        $stolpci = implode(', ', array_keys($podatki));
        $oznake = ':' . implode(', :', array_keys($podatki));
        
        $sql = "INSERT INTO $tabela ($stolpci) VALUES ($oznake)";
        $stmt = $this->povezava->prepare($sql);
        $stmt->execute($podatki);
        
        return $this->povezava->lastInsertId();
    }

    public function posodobi(string $tabela, array $podatki, array $pogoj, array $pogojParametri = []): int
    {
        $this->povezi();
        
        $set = [];
        foreach (array_keys($podatki) as $kljuc) {
            $set[] = "$kljuc = :$kljuc";
        }
        
        $where = [];
        foreach ($pogoj as $kljuc) {
            $where[] = "$kljuc = :_cond_$kljuc";
        }
        
        $sql = "UPDATE $tabela SET " . implode(', ', $set) . " WHERE " . implode(' AND ', $where);
        
        $parametri = $podatki;
        foreach ($pogoj as $kljuc) {
            $parametri["_cond_$kljuc"] = $pogojParametri[$kljuc] ?? null;
        }
        
        $stmt = $this->povezava->prepare($sql);
        $stmt->execute($parametri);
        
        return $stmt->rowCount();
    }

    public function zbrisi(string $tabela, array $pogoj, array $pogojParametri = []): int
    {
        $this->povezi();
        
        $where = [];
        foreach ($pogoj as $kljuc) {
            $where[] = "$kljuc = :_cond_$kljuc";
        }
        
        $sql = "DELETE FROM $tabela WHERE " . implode(' AND ', $where);
        
        $parametri = [];
        foreach ($pogoj as $kljuc) {
            $parametri["_cond_$kljuc"] = $pogojParametri[$kljuc] ?? null;
        }
        
        $stmt = $this->povezava->prepare($sql);
        $stmt->execute($parametri);
        
        return $stmt->rowCount();
    }

    public function transakcija_zacni(): void
    {
        $this->povezi();
        if (!$this->vTransakciji) {
            $this->povezava->beginTransaction();
            $this->vTransakciji = true;
        }
    }

    public function transakcija_potrdi(): void
    {
        if ($this->vTransakciji) {
            $this->povezava->commit();
            $this->vTransakciji = false;
        }
    }

    public function transakcija_preklici(): void
    {
        if ($this->vTransakciji) {
            $this->povezava->rollBack();
            $this->vTransakciji = false;
        }
    }

    public function tabela_obstaja(string $tabela): bool
    {
        $sql = "SHOW TABLES LIKE :tabela";
        $rezultat = $this->poizvedba_enega($sql, ['tabela' => $tabela]);
        return $rezultat !== null;
    }

    public function tabela_ustvari(string $tabela, array $stolpci): bool
    {
        $this->povezi();
        
        $definicije = [];
        foreach ($stolpci as $ime => $definicija) {
            $definicije[] = "`$ime` " . $definicija;
        }
        
        $sql = "CREATE TABLE IF NOT EXISTS `$tabela` (" . implode(', ', $definicije) . ") ENGINE=InnoDB DEFAULT CHARSET={$this->nastavitve['znakovni_nabor']}";
        
        $this->povezava->exec($sql);
        return true;
    }

    public function tabela_spusti(string $tabela): bool
    {
        $this->povezi();
        $this->povezava->exec("DROP TABLE IF EXISTS `$tabela`");
        return true;
    }

    public function zapri(): void
    {
        if ($this->vTransakciji) {
            $this->transakcija_preklici();
        }
        $this->povezava = null;
    }

    public function __destruct()
    {
        $this->zapri();
    }
}