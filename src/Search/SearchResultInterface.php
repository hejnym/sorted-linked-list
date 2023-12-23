<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search;

use Mano\SortedLinkedList\Node;

interface SearchResultInterface
{
    public function getNode(): Node;
}
