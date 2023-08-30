<?php

namespace Mano\SortedLinkedList\Search;

use Mano\SortedLinkedList\Node;

interface SearchInterface
{
    /**
     * @param mixed $data Data to be inserted
     */
    public function getNodeThatPrecedesNewOne(mixed $data, Node $startingNode): Node;

    public function getNodeNodeWithData(mixed $data, Node $head): ?Node;
}
