<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList;

use Mano\SortedLinkedList\Iterator\DataIterator;
use Mano\SortedLinkedList\Iterator\IteratorInterface;
use Mano\SortedLinkedList\Search\BuildAuxiliaryNodesInterface;
use Mano\SortedLinkedList\Search\SearchInterface;
use Mano\SortedLinkedList\Search\TraceableResultInterface;

/**
 * @implements \IteratorAggregate<?Node>
 */
class SortedLinkedList implements \IteratorAggregate
{
    private NodeFactory $factory;

    private Node $sentinelHead;

    private SearchInterface $search;

    public function __construct(
        SearchInterface $search,
    ) {
        $this->factory = new NodeFactory();
        $this->sentinelHead = $this->factory->createSentinelHead();
        $this->search = $search;
    }

    /**
     * @param array<mixed> $list
     */
    public function createFromArray(array $list): void
    {
        $this->sentinelHead = $this->factory->createHeadNodeFromArray($list);
    }

    public function push(mixed $data): void
    {
        $result = $this->search->getNodeThatPrecedes($data, $this->sentinelHead);

        $newlyInsertedNode = $this->factory->createAfterNode($result->getNode(), $data);

        if($this->search instanceof BuildAuxiliaryNodesInterface) {
            assert($result instanceof TraceableResultInterface);

            $this->search->insertAuxiliaryNodes(
                $result->getVisitedSkipNodesStack(),
                $newlyInsertedNode
            );
        }

        unset($result);
    }

    public function find(mixed $data): ?Node
    {

        $result = $this->search->getNodeThatPrecedes($data, $this->sentinelHead);

        $closestNode = $result->getNode();

        unset($result);

        if($closestNode->nextNode && $closestNode->nextNode->data === $data) {
            return $closestNode->nextNode;
        }

        return null;
    }

    public function delete(): void
    {
        // TODO - implement in similar manner as push
    }

    public function getIterator(): IteratorInterface
    {
        return new DataIterator($this->sentinelHead);
    }

    public function isEmpty(): bool
    {
        return (bool)$this->sentinelHead->nextNode?->isTailSentinel();
    }
}
