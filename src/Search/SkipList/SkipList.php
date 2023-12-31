<?php

declare(strict_types=1);

namespace Mano\SortedLinkedList\Search\SkipList;

use Mano\SortedLinkedList\Comparator\ComparatorInterface;
use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\Search\BuildAuxiliaryNodesInterface;
use Mano\SortedLinkedList\Search\SearchInterface;
use Mano\SortedLinkedList\Search\SearchResultInterface;

final class SkipList implements SearchInterface, BuildAuxiliaryNodesInterface
{
    private SkipNode $sentinelHead;

    public function __construct(
        private readonly ComparatorInterface $comparator,
        private readonly SkipNodeFactory $skipNodeFactory,
        private readonly LayerResolver $layerResolver,
    ) {
    }

    public function getNodeThatPrecedes(mixed $data, Node $startingNode): SearchResultInterface
    {
        $stack = new VisitedNodesStack();
        $maxSkipNodes = new VisitedNodesStack();

        if(isset($this->sentinelHead) === false) {
            // in case only search is tested, starting node is already created skip sentinel
            if($startingNode instanceof SkipNode) {
                $this->sentinelHead = $startingNode;
            } else {
                $this->sentinelHead = $this->skipNodeFactory->createSkipSentinelsInAllLayers($startingNode);
            }
        }

        $currentNode = $this->sentinelHead;

        while ($currentNode?->nextNode !== null) {
            $stack->push($currentNode);

            $isGreaterOrEqualToCurrentNode = $this->comparator->compare($data, $currentNode->data) !== -1;
            $isLessThanOrEqualToNextNode = $this->comparator->compare($data, $currentNode->nextNode?->data) !== 1;

            if ($isGreaterOrEqualToCurrentNode && $isLessThanOrEqualToNextNode) {
                if ($currentNode instanceof SkipNode) {
                    $maxSkipNodes->push($currentNode);
                    $currentNode = $currentNode->nextLayerNode;
                    continue;
                } else {
                    return new SkipListResult($currentNode, $stack, $maxSkipNodes);
                }
            }

            $currentNode = $currentNode->nextNode;
        }

        throw new \LogicException('Some node must be returned - the value must fit between sentinels.');
    }

    public function insertAuxiliaryNodes(VisitedNodesStack $visitedNodesStack, Node $newlyInsertedNode): void
    {
        $layersToSpan = $this->layerResolver->howManyLayersToSpan();
        if($layersToSpan === 0) {
            return;
        }

        $this->skipNodeFactory->createSkipNodeInMultipleLayers(
            $newlyInsertedNode,
            $visitedNodesStack,
            $layersToSpan
        );
    }
}
