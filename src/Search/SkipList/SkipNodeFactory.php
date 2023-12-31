<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search\SkipList;

use Mano\SortedLinkedList\Node;

class SkipNodeFactory
{
    public function __construct(private readonly LayerResolver $layerResolver)
    {
    }

    public function createSkipNodeInMultipleLayers(
        Node $newlyInsertedNode,
        VisitedNodesStack $visitedNodesStack,
    ): void {
        $layersToSpan = $this->layerResolver->howManyLayersToSpan();

        if($layersToSpan === 0) {
            return;
        }

        $previouslyCreatedNode = $newlyInsertedNode;

        $visitedNodesStack->rewind();
        for ($i = 0; $i < $layersToSpan; $i++) {
            $toprightSkipNodeInLayer = $visitedNodesStack->current();

            assert($toprightSkipNodeInLayer instanceof SkipNode);

            $previouslyCreatedNode = $this->createSkipNode($previouslyCreatedNode, $toprightSkipNodeInLayer);

            $visitedNodesStack->next();
        }
    }

    private function createSkipNode(Node $nextLayerNode, SkipNode $nextSkipNode): SkipNode
    {
        assert($nextSkipNode->nextNode instanceof SkipNode);

        $newSkipNode = new SkipNode($nextLayerNode, $nextSkipNode->nextNode);

        $nextSkipNode->nextNode = $newSkipNode;

        return $newSkipNode;
    }

    public function createSkipSentinelsInAllLayers(Node $startingNode): SkipNode
    {
        $deepestHeadSentinel = $startingNode;
        // Sentinel tail will point to the same tail node, but it does not matter.
        $deepestTailSentinel = new Node(INF, null);

        for($i = 0; $i < $this->layerResolver->getMaxLayers(); $i++) {

            if($i > 1) {
                assert($deepestHeadSentinel instanceof SkipNode);
            }

            assert($deepestHeadSentinel->isHeadSentinel());

            $deepestHeadSentinel = new SkipNode(
                $deepestHeadSentinel,
                new SkipNode($deepestTailSentinel)
            );
        }

        assert($deepestHeadSentinel instanceof SkipNode);

        return $deepestHeadSentinel;
    }
}
