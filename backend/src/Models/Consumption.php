<?php
namespace App\Models;

use DateTime;

class Consumption {
    static public int $MAX_CAFFEINE_PER_DAY_MG = 400;

    public function __construct(
        public ?int $id,
        public int $drinkId,
        public string $developerName,
        public string $consumedAt,
    ) {}
}