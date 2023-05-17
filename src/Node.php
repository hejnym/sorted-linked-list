<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList;

class Node
{
    public int|string $data;
    public ?Node $nextNode = null;

    public function __construct(int|string $data, ?Node $nextNode)
    {
        $this->data = $data;
        $this->nextNode = $nextNode;
    }
}
