<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Iterator;

use Mano\SortedLinkedList\Node;

class DataIterator implements IteratorInterface
{
    private NodeIterator $nodeIterator;

    public function __construct(
        private readonly Node $head
    ) {
        $this->nodeIterator = new NodeIterator($this->head, true);
    }

    public function current(): mixed
    {
        /** @var ?Node $node */
        $node = $this->nodeIterator->current();

        return $node?->data;
    }

    public function next(): void
    {
        $this->nodeIterator->next();
    }

    public function key(): mixed
    {
        return $this->nodeIterator->key();
    }

    public function valid(): bool
    {
        return $this->nodeIterator->valid();
    }

    public function rewind(): void
    {
        $this->nodeIterator->rewind();
    }
}
