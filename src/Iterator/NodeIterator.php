<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Iterator;

use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\SortedLinkedList;

class NodeIterator implements IteratorInterface
{
    private ?Node $pointer;

    public function __construct(
        private readonly ?Node $head
    ) {
        $this->pointer = $head;
    }

    public function current(): Node|null
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
        $this->pointer = $this->head;
    }

    public function key(): null
    {
        return null;
    }
}
