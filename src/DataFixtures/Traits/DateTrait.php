<?php

namespace App\DataFixtures\Traits;

use DateTimeImmutable;

trait DateTrait
{
    private function createRandomDate(?DateTimeImmutable $startDate = null): DateTimeImmutable
    {
        $start = $startDate ? $startDate->format('Y-m-d H:i:s') : '-6 months';

        $dateTime = $this->faker->dateTimeBetween($start, 'now');

        return DateTimeImmutable::createFromMutable($dateTime);
    }
}
