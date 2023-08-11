<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList;

use Mano\SortedLinkedList\Comparator\Alpanuberic;
use Mano\SortedLinkedList\Comparator\ComparatorInterface;
use Mano\SortedLinkedList\Iterator\DataIterator;
use Mano\SortedLinkedList\Iterator\IteratorInterface;
use Mano\SortedLinkedList\Iterator\NodeIterator;

/**
 * @implements \IteratorAggregate<?Node>
 */
class SortedLinkedList implements \IteratorAggregate
{
    private NodeFactory $factory;

    private ?Node $head = null;

    private NodeIterator $nodeIterator;
    private ComparatorInterface $comparator;

    public function __construct(
        NodeFactory $factory,
        ?ComparatorInterface $comparator = null
    ) {
        $this->factory = $factory;
        $this->nodeIterator = new NodeIterator($this);
        $this->comparator = $comparator ?? new Alpanuberic();
    }

    /**
     * @param array<int|string> $list
     */
    public function createFromArray(array $list): void
    {
        $this->head = $this->factory->createHeadNodeFromArray($list);
        $this->nodeIterator->rewind();
    }

    public function push(mixed $data): void
    {
        $isLessThanInitialNode = $this->comparator->compare($data, $this->head?->data) === -1;

        if ($this->isEmpty() || $isLessThanInitialNode) {
            $this->head = $this->factory->createNode($data, $this->head);
            return;
        }

        /** @var Node $node */
        foreach ($this->nodeIterator as $node) {
            if($node->isLast()) {
                $this->factory->createAfterNode($node, $data);
                return;
            }

            $isGreaterOrEqualToCurrentNode = $this->comparator->compare($data, $node->data) !== -1;
            $isLessThanOrEqualToNextNode = $this->comparator->compare($data, $node->nextNode?->data) !== 1;

            if ($isGreaterOrEqualToCurrentNode && $isLessThanOrEqualToNextNode) {
                $this->factory->createAfterNode($node, $data);
                return;
            }
        }

        throw new \LogicException('Node must be always created, this must be never reached.');
    }

    public function delete(): void
    {
        // TODO - implement in similar manner as push
    }

    public function getIterator(): IteratorInterface
    {
        return new DataIterator($this);
    }

    public function isEmpty(): bool
    {
        return $this->head === null;
    }

    public function getHead(): ?Node
    {
        return $this->head;
    }
}
