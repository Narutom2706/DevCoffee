<?php
namespace App\Models;

use DateTime;

class Consumption {
    public function __construct(
        public ?int $id,
        public int $drinkId,
        public string $developerName,
        public DateTime $consumedAt,
    ) {}
}