<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList;

/**
 * @implements \Iterator<?Node>
 */
class SortedLinkedList implements \Iterator
{
    private NodeFactory $factory;

    private ?Node $head = null;
    private ?Node $pointer = null;

    public function __construct(NodeFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param array<int|string> $list
     */
    public function createFromArray(array $list): void
    {
        $this->head = $this->factory->createHeadNodeFromArray($list);
        $this->rewind();
    }

    public function getData(): \Generator
    {
        if($this->isEmpty()) {
            return null;
        }

        /** @var Node $node */
        foreach ($this as $node) {
            yield $node->data;
        }
    }

    public function push(int|string $data): void
    {
        if ($this->isEmpty()) {
            $this->head = $this->factory->createNode($data, null);
            $this->rewind();

            return;
        }

        if($this->head && $data < $this->head->data) {
            $this->head = $this->factory->createNode($data, $this->head);
            return;
        }

        /** @var Node $node */
        foreach ($this as $node) {
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

    public function isEmpty(): bool
    {
        return $this->head === null;
    }

    public function current(): mixed
    {
        return $this->pointer;
    }

    public function next(): void
    {
        if($this->pointer) {
            $this->pointer = $this->pointer->nextNode;
        }
    }

    public function valid(): bool
    {
        return $this->pointer !== null;
    }

    public function rewind(): void
    {
        $this->pointer = $this->head;
    }

    public function key(): mixed
    {
        return null;
    }
}
