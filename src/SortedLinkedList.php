<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList;

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

    public function __construct(NodeFactory $factory)
    {
        $this->factory = $factory;

        $this->nodeIterator = new NodeIterator($this);
    }

    /**
     * @param array<int|string> $list
     */
    public function createFromArray(array $list): void
    {
        $this->head = $this->factory->createHeadNodeFromArray($list);
        $this->nodeIterator->rewind();
    }

    public function push(int|string $data): void
    {
        if ($this->isEmpty()) {
            $this->head = $this->factory->createNode($data, null);
            $this->nodeIterator->rewind();

            return;
        }

        if($this->head && $data < $this->head->data) {
            $this->head = $this->factory->createNode($data, $this->head);
            return;
        }

        /** @var Node $node */
        foreach ($this->nodeIterator as $node) {
            if($node->nextNode === null) {
                $this->factory->createAfterNode($node, $data);
                return;
            }

            if ($data >= $node->data && $data <= $node->nextNode->data) {
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
