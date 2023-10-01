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

    public function getNodeThatPrecedesNewOne(mixed $data, Node $startingNode): SearchResultInterface
    {
        $result = $this->compare($startingNode, function (Node $node) use ($data) {
            if($node->isLast()) {
                return $node;
            }

            $isGreaterOrEqualToCurrentNode = $this->comparator->compare($data, $node->data) !== -1;
            $isLessThanOrEqualToNextNode = $this->comparator->compare($data, $node->nextNode?->data) !== 1;

            if ($isGreaterOrEqualToCurrentNode && $isLessThanOrEqualToNextNode) {
                return $node;
            }

            return null;
        });

        if($result === null) {
            throw new \LogicException('Some node must be returned');
        }

        return $result;
    }

    public function getNodeNodeWithData(mixed $data, Node $head): ?SearchResultInterface
    {
        return $this->compare($head, function (Node $node) use ($data) {
            if ($this->comparator->compare($data, $node->data) === 0) {
                return $node;
            }
        });
    }

    private function compare(Node $startingNode, callable $f): ?SearchResultInterface
    {
        $this->nodeIterator = new NodeIterator($startingNode);

        foreach ($this->nodeIterator as $node) {
            $result = $f($node);
            if($result !== null) {
                return new LinearSearchResult($result);
            }
        }

        return null;
    }
}
