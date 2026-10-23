<?php
namespace App\Services;
use App\Models\Consumption;
use App\Models\Drink;
use DateTime;
use PDO;

class CoffeeService {
    public function __construct(
        private PDO $pdo
    ) {}
    public function getAllDrinks(): array {
        $req = $this->pdo->query('SELECT id, name, caffeine_mg, volume_ml FROM drinks');
        $rows = $req->fetchAll();

        return array_map(fn($r) => new Drink(
            (int)$r['id'],
            $r['name'],
            (int)$r['caffeine_mg'],
            (int)$r['volume_ml']
        ), $rows);
    }

    public function getDrinkById(int $id): ?Drink {
        $req = $this->pdo->prepare('SELECT id, name, caffeine_mg, volume_ml FROM drinks WHERE id = :id');
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        $req->execute();
        $r = $req->fetch();
        
        if (!$r) return null;

        return new Drink(
            (int)$r['id'],
            $r['name'],
            (int)$r['caffeine_mg'],
            (int)$r['volume_ml']
        );
    }

    public function getDrinkCaffeineById(int $id): ?int {
        $req = $this->pdo->prepare('SELECT caffeine_mg FROM drinks WHERE id = :id');
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        $req->execute();
        $r = $req->fetch();
        
        if (!$r) return null;

        return (int)$r['caffeine_mg'];
    }

    public function createDrink(string $name, int $caffeineMg, int $volumeMl): Drink {
        $req = $this->pdo->prepare('
            INSERT INTO drinks (name, caffeine_mg, volume_ml)
            VALUES (:name, :caffeineMg, :volumeMl)
        ');
        $req->bindValue(':name', $name, PDO::PARAM_STR);
        $req->bindValue(':caffeineMg', $caffeineMg, PDO::PARAM_INT);
        $req->bindValue(':volumeMl', $volumeMl, PDO::PARAM_INT);
        $req->execute();

        $id = (int)$this->pdo->lastInsertId();

        return new Drink($id, $name, $caffeineMg, $volumeMl);
    }

    public function getAllConsumptions(): array {
        $req = $this->pdo->query('SELECT id, drink_id, developer_name, consumed_at FROM consumptions ORDER BY consumed_at DESC LIMIT 20');
        $rows = $req->fetchAll();

        return array_map(fn($r) => new Consumption(
            (int)$r['id'],
            (int)$r['drink_id'],
            $r['developer_name'],
            (int)$r['consumed_at']
        ), $rows);
    }

    public function getConsumptionById(int $id): ?Consumption {
        $req = $this->pdo->prepare('SELECT id, drink_id, developer_name, consumed_at FROM consumptions WHERE id = :id');
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        $req->execute();
        $r = $req->fetch();
        
        if (!$r) return null;

        return new Consumption(
            (int)$r['id'],
            (int)$r['drink_id'],
            $r['developer_name'],
            (int)$r['consumed_at']
        );
    }

    public function createConsumption(int $drinkId, string $developerName): ?Consumption {
        $consumedAt = (new DateTime())->format('Y-m-d H:i:s');
        $drinkCaffeine = $this->getDrinkCaffeineById($drinkId);

        if (!$drinkCaffeine || ($this->getDeveloperCaffeineConsumedByDate($developerName, $consumedAt) + $drinkCaffeine) > Consumption::$MAX_CAFFEINE_PER_DAY_MG ) {
            return null;
        }

        $req = $this->pdo->prepare('
            INSERT INTO consumptions (drink_id, developer_name, consumed_at)
            VALUES (:drinkId, :developerName, :consumedAt)
        ');
        $req->bindValue(':drinkId', $drinkId, PDO::PARAM_INT);
        $req->bindValue(':developerName', $developerName, PDO::PARAM_STR);
        $req->bindValue(':consumedAt', $consumedAt, PDO::PARAM_STR);
        $req->execute();

        $id = (int)$this->pdo->lastInsertId();

        return new Consumption($id, $drinkId, $developerName, $consumedAt);
    }

    public function deleteConsumptionById(int $id): bool {
        $req = $this->pdo->prepare('DELETE FROM consumptions WHERE id = :id');
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        $req->execute();

        return $req->rowCount() > 0;
    }

    public function getDeveloperCaffeineConsumedByDate(string $developerName, string $date): int {
        $req = $this->pdo->prepare('
            SELECT drinks.caffeine_mg FROM drinks
            JOIN consumptions ON consumptions.drink_id = drinks.id
            WHERE developer_name = :developerName
            AND consumed_at >= :morningDate
            AND consumed_at <= :date
        ');

        $req->bindValue(':developerName', $developerName, PDO::PARAM_INT);
        $req->bindValue(':moringDate', $date->format('Y-m-d').' 00:00:00', PDO::PARAM_STR);
        $req->bindValue(':date', $date, PDO::PARAM_STR);

        $req->execute();
        $rows = $req->fetchAll();

        $caffeineConsumed = 0;

        foreach ($rows as $row) {
            $caffeineConsumed += (int)$row['caffeine_mg'];
        }

        return $caffeineConsumed;
    }
}