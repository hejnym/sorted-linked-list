<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search\SkipList;

use Mano\SortedLinkedList\Node;

/**
 * @extends \SplStack<Node>
 */
class VisitedNodesStack extends \SplStack
{
    public function __construct(Node ...$skipNode)
    {
        foreach ($skipNode as $node) {
            $this->push($node);
        }
    }

    public function getLastSkipNode(): ?SkipNode
    {

        $this->rewind();

        while($this->valid()) {
            if($this->current() instanceof SkipNode) {
                return $this->current();
            }
            $this->next();
        }

        return null;
    }
}
