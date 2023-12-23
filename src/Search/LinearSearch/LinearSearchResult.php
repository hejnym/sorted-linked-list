<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search\LinearSearch;

use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\Search\SearchResultInterface;

class LinearSearchResult implements SearchResultInterface
{
    public function __construct(private readonly Node $node)
    {
    }

    public function getNode(): Node
    {
        return $this->node;
    }
}
