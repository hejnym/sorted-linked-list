<?php

namespace Mano\SortedLinkedList\Search;

use Mano\SortedLinkedList\Node;

interface SearchInterface
{
    /**
     * @param mixed $data Data to be inserted
     * @param Node $startingNode Initial node
     * @return Node|null Null returned when new element will be the first  and therefore nothing precedes it.
     */
    public function getNodeThatPrecedesNewOne(mixed $data, Node $startingNode): ?Node;

    public function getNodeNodeWithData(mixed $data, Node $head): ?Node;
}
