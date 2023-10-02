<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search\SkipList;

use Mano\SortedLinkedList\Node;

class SkipNode extends Node
{
    public Node $nextLayerNode;

    public function __construct(mixed $data, Node $nextLayerNode, ?Node $nextNode = null)
    {
        $this->nextLayerNode = $nextLayerNode;

        parent::__construct($data, $nextNode);
    }
}
