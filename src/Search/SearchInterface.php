<?php

namespace Mano\SortedLinkedList\Search;

use Mano\SortedLinkedList\Node;

interface SearchInterface
{
    /**
     * @param mixed $data Data to be inserted
     */
    public function getNodeThatPrecedes(mixed $data, Node $startingNode): SearchResultInterface;
}
