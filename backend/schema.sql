CREATE DATABASE IF NOT EXISTS devcoffee CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE devcoffee;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS consumptions;
DROP TABLE IF EXISTS drinks;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Table drinks (Catalogue des boissons)
CREATE TABLE IF NOT EXISTS drinks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    caffeine_mg INT NOT NULL,
    volume_ml INT NOT NULL
) ENGINE=InnoDB;

-- 2. Table consumptions (Historique des consommations)
CREATE TABLE IF NOT EXISTS consumptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    drink_id INT NOT NULL,
    developer_name VARCHAR(100) NOT NULL,
    consumed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_consumptions_drink FOREIGN KEY (drink_id) REFERENCES drinks(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Jeu de données de test initial dans drinks
INSERT INTO drinks (id, name, caffeine_mg, volume_ml) VALUES
(1, 'Espresso', 80, 30),
(2, 'Café Allongé', 110, 120),
(3, 'Monster Energy', 160, 500),
(4, 'Double Espresso', 150, 60),
(5, 'Cappuccino', 90, 150),
(6, 'Thé Vert Matcha', 70, 250);

-- Consommations initiales de test
INSERT INTO consumptions (drink_id, developer_name, consumed_at) VALUES
(1, 'Thomas', DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(2, 'Sarah', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(1, 'Alexandre', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(5, 'Julie', DATE_SUB(NOW(), INTERVAL 30 MINUTE));
