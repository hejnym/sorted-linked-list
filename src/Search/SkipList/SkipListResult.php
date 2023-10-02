<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search\SkipList;

use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\Search\SearchResultInterface;

class SkipListResult implements SearchResultInterface
{
    public function __construct(
        private readonly Node $node,
        private readonly VisitedNodesStack $visitedSkipNodesStack
    ) {
    }

    public function getVisitedSkipNodesStack(): VisitedNodesStack
    {
        return $this->visitedSkipNodesStack;
    }

    public function getResult(): Node
    {
        return $this->node;
    }
}
