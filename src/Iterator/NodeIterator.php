<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Iterator;

use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\SortedLinkedList;

class NodeIterator implements IteratorInterface
{
    private ?Node $pointer;

    public function __construct(
        private readonly SortedLinkedList $list
    ) {
        $this->pointer = $this->list->getHead();
    }

    public function current(): mixed
    {
        return $this->pointer;
    }

    public function next(): void
    {
        if($this->pointer) {
            $this->pointer = $this->pointer->nextNode;
        }
    }

    public function valid(): bool
    {
        return $this->pointer !== null;
    }

    public function rewind(): void
    {
        $this->pointer = $this->list->getHead();
    }

    public function key(): mixed
    {
        return null;
    }
}
