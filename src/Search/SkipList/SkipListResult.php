<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search\SkipList;

use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\Search\SearchResultInterface;
use Mano\SortedLinkedList\Search\TraceableResultInterface;

class SkipListResult implements SearchResultInterface, TraceableResultInterface
{
    public function __construct(
        private readonly Node $node,
        private readonly VisitedNodesStack $allVisitedSkipNodesStack,
        private readonly VisitedNodesStack $maxSkipNodesStack
    ) {
    }

    public function getVisitedSkipNodesStack(): VisitedNodesStack
    {
        return $this->allVisitedSkipNodesStack;
    }

    public function getNode(): Node
    {
        return $this->node;
    }

    /**
     * @return VisitedNodesStack
     */
    public function getMaxSkipNodesStack(): VisitedNodesStack
    {
        return $this->maxSkipNodesStack;
    }
}
