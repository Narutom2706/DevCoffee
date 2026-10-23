<?php
namespace App\Models;

class Consumption {
    public static int $MAX_CAFFEINE_PER_DAY_MG = 400;

    public function __construct(
        public ?int $id,
        public int $drinkId,
        public string $developerName,
        public string $consumedAt,
        public ?string $drinkName = null,
        public ?int $caffeineMg = null,
    ) {}
}