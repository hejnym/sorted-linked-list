<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search\SkipList;

use Mano\SortedLinkedList\Node;

class SkipNode extends Node
{
    public Node $nextLayerNode;

    public function __construct(Node $nextLayerNode, ?SkipNode $nextSkipNode = null)
    {
        parent::__construct($nextLayerNode->data, $nextSkipNode);

        $this->nextLayerNode = $nextLayerNode;
    }
}
