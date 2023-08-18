<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList;

use Mano\SortedLinkedList\Iterator\DataIterator;
use Mano\SortedLinkedList\Iterator\IteratorInterface;
use Mano\SortedLinkedList\Search\SearchInterface;

/**
 * @implements \IteratorAggregate<?Node>
 */
class SortedLinkedList implements \IteratorAggregate
{
    private NodeFactory $factory;

    private ?Node $head = null;

    private SearchInterface $search;

    public function __construct(
        SearchInterface $search,
    ) {
        $this->factory = new NodeFactory();
        $this->search = $search;
    }

    /**
     * @param array<mixed> $list
     */
    public function createFromArray(array $list): void
    {
        $this->head = $this->factory->createHeadNodeFromArray($list);
    }

    public function push(mixed $data): void
    {
        if ($this->isEmpty()) {
            $this->head = $this->factory->createNode($data, $this->head);
            return;
        }

        if($this->head === null) {
            throw new \LogicException('Head must exist at this point.');
        }

        $closestNode = $this->search->getNodeThatPrecedesNewOne($data, $this->head);

        if($closestNode === null) {
            $this->head = $this->factory->createNode($data, $this->head);
        } else {
            $this->factory->createAfterNode($closestNode, $data);
        }

    }

    public function delete(): void
    {
        // TODO - implement in similar manner as push
    }

    public function getIterator(): IteratorInterface
    {
        return new DataIterator($this->head);
    }

    public function isEmpty(): bool
    {
        return $this->head === null;
    }
}
