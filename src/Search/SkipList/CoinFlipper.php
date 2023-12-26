<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search\SkipList;

class CoinFlipper
{
    public function __construct(
        readonly private float $chanceOfHead = 0.2
    ) {
    }

    public function isHead(): bool
    {
        return rand(0, 100)/100 < $this->chanceOfHead;
    }
}
