<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList;

class Node
{
    private const HEAD_SENTINEL_VALUE = -INF;
    private const TAIL_SENTINEL_VALUE = INF;

    public function __construct(
        public mixed $data,
        public ?Node $nextNode = null
    ) {
    }

    public function isLast(): bool
    {
        return $this->nextNode === null;
    }

    public function isHeadSentinel(): bool
    {
        return $this->data === self::HEAD_SENTINEL_VALUE;
    }

    public function isTailSentinel(): bool
    {
        return $this->data === self::TAIL_SENTINEL_VALUE;
    }
}
