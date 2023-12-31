<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search\SkipList;

class LayerResolver
{
    public function __construct(
        readonly private float $chanceOfHead = 0.2,
        readonly private int $maxLayers = 5
    ) {
    }

    private function getRandomValue(): int
    {
        return rand(0, 100);
    }

    public function howManyLayersToSpan(): int
    {
        $layers = 0;

        while ($this->getRandomValue() /100 < $this->chanceOfHead) {
            $layers++;
        }

        return min($layers, $this->maxLayers);
    }

    /**
     * @return int
     */
    public function getMaxLayers(): int
    {
        return $this->maxLayers;
    }
}
