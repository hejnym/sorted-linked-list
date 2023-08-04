<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList;

class Node
{
    public function __construct(
        public int|string $data,
        public ?Node $nextNode = null
    ) {
    }
}
