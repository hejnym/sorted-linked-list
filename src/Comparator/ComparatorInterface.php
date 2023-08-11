<?php

namespace Mano\SortedLinkedList\Comparator;

interface ComparatorInterface
{
    public function compare(mixed $a, mixed $b): int;
}
