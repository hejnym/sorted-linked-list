<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search;

use Mano\SortedLinkedList\Comparator\ComparatorInterface;
use Mano\SortedLinkedList\Iterator\NodeIterator;
use Mano\SortedLinkedList\Node;

class LinearSearch implements SearchInterface
{
    private NodeIterator $nodeIterator;

    public function __construct(
        private readonly ComparatorInterface $comparator
    ) {

    }

    public function getNodeThatPrecedesNewOne(mixed $data, Node $head): ?Node
    {
        $this->nodeIterator = new NodeIterator($head);

        $isLessThanInitialNode = $this->comparator->compare($data, $head->data) === -1;

        if($isLessThanInitialNode) {
            return null;
        }

        /** @var Node $head */
        foreach ($this->nodeIterator as $head) {
            if($head->isLast()) {
                return $head;
            }

            $isGreaterOrEqualToCurrentNode = $this->comparator->compare($data, $head->data) !== -1;
            $isLessThanOrEqualToNextNode = $this->comparator->compare($data, $head->nextNode?->data) !== 1;

            if ($isGreaterOrEqualToCurrentNode && $isLessThanOrEqualToNextNode) {
                return $head;
            }
        }

        throw new \LogicException('This can not happen.');
    }
}
