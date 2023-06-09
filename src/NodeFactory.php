<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList;

class NodeFactory
{
    public function createNode(int|string $data, ?Node $nextNode): Node
    {
        return new Node($data, $nextNode);
    }

    public function createAfterNode(Node $node, int|string $data): Node
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
     * @param array<int|string> $list
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
                $currentNode = $this->createNode($element, null);
                continue;
            }

            $currentNode = $this->createNode($element, $currentNode);
        }

        return $currentNode;
    }
}
