<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search\LinearSearch;

use Mano\SortedLinkedList\Comparator\ComparatorInterface;
use Mano\SortedLinkedList\Iterator\NodeIterator;
use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\Search\SearchInterface;
use Mano\SortedLinkedList\Search\SearchResultInterface;

class LinearSearch implements SearchInterface
{
    private NodeIterator $nodeIterator;

    public function __construct(
        private readonly ComparatorInterface $comparator
    ) {
    }

    public function getNodeThatPrecedes(mixed $data, Node $startingNode): SearchResultInterface
    {
        $this->nodeIterator = new NodeIterator($startingNode);

        foreach ($this->nodeIterator as $node) {
            $isGreaterOrEqualToCurrentNode = $this->comparator->compare($data, $node->data) !== -1;
            $isLessThanOrEqualToNextNode = $this->comparator->compare($data, $node->nextNode?->data) !== 1;

            if ($isGreaterOrEqualToCurrentNode && $isLessThanOrEqualToNextNode) {
                return new LinearSearchResult($node);
            }
        }

        throw new \LogicException('Some node must be returned - the value must fit between sentinels.');
    }
}
