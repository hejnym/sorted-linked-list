<?php

namespace Mano\SortedLinkedList\Search;

use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\Search\SkipList\VisitedNodesStack;

interface BuildAuxiliaryNodesInterface
{
    public function insertAuxiliaryNodes(VisitedNodesStack $visitedNodesStack, Node $newlyInsertedNode): void;
}
