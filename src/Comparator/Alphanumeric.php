<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Comparator;

class Alphanumeric implements ComparatorInterface
{
    public function compare(mixed $a, mixed $b): int
    {
        if($a === INF || $b === -INF) {
            return 1;
        }

        if($a === -INF || $b === INF) {
            return -1;
        }

        return $a <=> $b;
    }
}
