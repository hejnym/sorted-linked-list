<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search\SkipList;

use Mano\SortedLinkedList\Comparator\ComparatorInterface;
use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\Search\LinearSearch\LinearSearchResult;
use Mano\SortedLinkedList\Search\SearchInterface;
use Mano\SortedLinkedList\Search\SearchResultInterface;

final class SkipList implements SearchInterface
{
    public function __construct(private readonly ComparatorInterface $comparator)
    {
    }

    public function getNodeThatPrecedes(mixed $data, Node $startingNode): SearchResultInterface
    {
        $currentNode = $startingNode;

        $stack = new VisitedNodesStack();

        while ($currentNode?->nextNode !== null) {
            $stack->push($currentNode);

            $isGreaterOrEqualToCurrentNode = $this->comparator->compare($data, $currentNode->data) !== -1;
            $isLessThanOrEqualToNextNode = $this->comparator->compare($data, $currentNode->nextNode?->data) !== 1;

            if ($isGreaterOrEqualToCurrentNode && $isLessThanOrEqualToNextNode) {
                if ($currentNode instanceof SkipNode) {
                    $currentNode = $currentNode->nextLayerNode;
                    continue;
                } else {
                    return new SkipListResult($currentNode, $stack);
                }
            }

            $currentNode = $currentNode->nextNode;
        }

        throw new \LogicException('Some node must be returned - the value must fit between sentinels.');
    }
}
