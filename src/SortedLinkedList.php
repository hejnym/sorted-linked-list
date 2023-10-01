<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList;

use Mano\SortedLinkedList\Iterator\DataIterator;
use Mano\SortedLinkedList\Iterator\IteratorInterface;
use Mano\SortedLinkedList\Iterator\NodeIterator;
use Mano\SortedLinkedList\Search\SearchInterface;

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
        $closestNode = $this->search->getNodeThatPrecedesNewOne($data, $this->sentinelHead)->getResult();
        $this->factory->createAfterNode($closestNode, $data);
    }

    public function find(mixed $data): ?Node
    {
        return $this->search->getNodeNodeWithData($data, $this->sentinelHead)?->getResult();
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
