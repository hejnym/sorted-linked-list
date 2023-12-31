<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search;

use Mano\SortedLinkedList\Search\SkipList\VisitedNodesStack;

interface TraceableResultInterface
{
    public function getVisitedSkipNodesStack(): VisitedNodesStack;
    public function getMaxSkipNodesStack(): VisitedNodesStack;
}
