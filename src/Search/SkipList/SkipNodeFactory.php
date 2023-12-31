<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search\SkipList;

use Mano\SortedLinkedList\Node;

class SkipNodeFactory
{
	public function __construct(private readonly LayerResolver $layerResolver)
	{
	}

	public function createSkipNode(Node $finalNode, SkipNode $lastSkipNode): void
    {
        assert($lastSkipNode->nextNode instanceof SkipNode);

        $newSkipNode =  new SkipNode($finalNode, $lastSkipNode->nextNode);

        $lastSkipNode->nextNode = $newSkipNode;
    }

	public function createSkipSentinelsInAllLayers(Node $startingNode): SkipNode
	{
		assert($startingNode instanceof SkipNode === false);

		$deepestHeadSentinel = $startingNode;
		// Sentinel tail will point to the same tail node, but it does not matter.
		$deepestTailSentinel = new Node(INF, null);

		for($i = 0; $i < $this->layerResolver->getMaxLayers(); $i++) {

			if($i > 1) {
				assert($startingNode instanceof SkipNode);
			}

			assert($deepestHeadSentinel->isHeadSentinel());

			$deepestHeadSentinel = new SkipNode(
				$deepestHeadSentinel,
				new SkipNode($deepestTailSentinel)
			);
		}

		return $deepestHeadSentinel;
	}
}
