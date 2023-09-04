<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList;

class NodeFactory
{
    public function createNode(mixed $data, ?Node $nextNode): Node
    {
        return new Node($data, $nextNode);
    }

    public function createSentinelHead(?Node $beforeNode = null): Node
    {
        if($beforeNode) {
            return self::createNode(-INF, $beforeNode);
        }

        return self::createNode(-INF, self::createNode(INF, null));
    }

    public function createAfterNode(Node $node, mixed $data): Node
    {
        if($node->nextNode) {
            $newNode = $this->createNode($data, $node->nextNode);
        } else {
            $newNode = $this->createNode($data, null);
        }

        $node->nextNode = $newNode;

        return $newNode;
    }

    /**
     * @param array<mixed> $list
     */
    public function createHeadNodeFromArray(array $list): Node
    {
        if(!$list) {
            throw new \InvalidArgumentException();
        }

        rsort($list);

        $currentNode = null;

        foreach ($list as $index => $element) {
            if ($index === array_key_first($list)) {
                $currentNode = $this->createNode($element, self::createNode(INF, null));
                continue;
            }

            $currentNode = $this->createNode($element, $currentNode);
        }

        return $this->createSentinelHead($currentNode);
    }
}
