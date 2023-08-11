<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Comparator;

class Alpanuberic implements ComparatorInterface
{
    public function compare(mixed $a, mixed $b): int
    {
        return $a <=> $b;
    }
}
