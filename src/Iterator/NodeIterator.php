<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Iterator;

use Mano\SortedLinkedList\Node;

class NodeIterator implements IteratorInterface
{
    private ?Node $pointer;

    private int $index = 0;

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
        if($this->pointer === null) {
            throw new \RuntimeException('Can not call current after last node has been reached.');
        }

        if($this->skipSentinels) {
            if($this->pointer->isHeadSentinel()) {
                $this->next();
            }

            if($this->isEmptyList()) {
                throw new \RuntimeException('Can not call current on empty list.');
            }

            if($this->pointer?->isTailSentinel()) {
                throw new \RuntimeException('Can not call current on tail sentinel.');
            }
        }

        return $this->pointer;
    }

    public function next(): void
    {
        $this->pointer = $this->pointer?->nextNode;
    }

    public function valid(): bool
    {
        if($this->skipSentinels) {
            if($this->isEmptyList()) {
                return false;
            }

            return $this->pointer?->isTailSentinel() === false;

        } else {
            return $this->pointer !== null;
        }
    }

    public function rewind(): void
    {
        $this->pointer = $this->sentinelHead;
    }

    public function key(): int
    {
        return $this->index++;
    }

    private function isEmptyList(): bool
    {
        return $this->skipSentinels && $this->pointer?->isHeadSentinel() && $this->pointer->nextNode?->isTailSentinel();
    }

}
