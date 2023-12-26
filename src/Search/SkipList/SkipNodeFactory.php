<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search\SkipList;

use Mano\SortedLinkedList\Node;

class SkipNodeFactory
{
    public function createSkipNode(Node $finalNode, SkipNode $lastSkipNode): void
    {
        assert($lastSkipNode->nextNode instanceof SkipNode);

        $newSkipNode =  new SkipNode($finalNode, $lastSkipNode->nextNode);

        $lastSkipNode->nextNode = $newSkipNode;
    }

    public function createSentinelHead(Node $startingNode): SkipNode
    {
        assert($startingNode->isHeadSentinel());

        // testing purposes
        if ($startingNode instanceof SkipNode) {
            return $startingNode;
        }

        return new SkipNode(
            $startingNode,
            // Sentinel tail won't point to the same tail node, but it does not matter.
            new SkipNode(new Node(INF, null))
        );
    }
}
