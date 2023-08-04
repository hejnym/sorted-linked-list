<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Iterator;

use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\SortedLinkedList;

class DataIterator implements IteratorInterface
{
    private NodeIterator $nodeIterator;
    private int $index = 0;

    public function __construct(
        private readonly SortedLinkedList $list
    ) {
        $this->nodeIterator = new NodeIterator($this->list);
    }

    public function current(): string|int|null
    {
        /** @var ?Node $node */
        $node = $this->nodeIterator->current();

        if($node === null) {
            return null;
        }

        return $node->data;
    }

    public function next(): void
    {
        $this->nodeIterator->next();
    }

    public function key(): mixed
    {
        return $this->index++;
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
