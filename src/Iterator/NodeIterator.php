<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Iterator;

use Mano\SortedLinkedList\Node;

class NodeIterator implements IteratorInterface
{
    private ?Node $pointer;

    public function __construct(
        private readonly Node $sentinelHead,
        private readonly bool $skipSentinels = false
    ) {
        $this->pointer = $sentinelHead;

        if(!$sentinelHead->isHeadSentinel()) {
            throw new \InvalidArgumentException('First item must be sentinel');
        }
    }

    public function current(): Node
    {
        if($this->sentinelShouldBeSkipped()) {
            $this->next();
        }

        if($this->pointer === null) {
            throw new \RuntimeException('Can not call current on empty iterator.');
        }

        return $this->pointer;
    }

    public function next(): void
    {
        $this->pointer = $this->pointer?->nextNode;
    }

    public function valid(): bool
    {
        if($this->sentinelShouldBeSkipped() && $this->pointer?->nextNode === null) {
            return false;
        }

        return $this->pointer !== null;
    }

    public function rewind(): void
    {
        $this->pointer = $this->sentinelHead;
    }

    public function key(): null
    {
        return null;
    }

    private function sentinelShouldBeSkipped(): bool
    {
        return $this->skipSentinels && $this->pointer?->isHeadSentinel();
    }
}
