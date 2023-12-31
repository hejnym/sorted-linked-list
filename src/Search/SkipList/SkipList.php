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
        private readonly SkipNodeFactory $skipNodeFactory
    ) {
    }

    public function getNodeThatPrecedes(mixed $data, Node $startingNode): SearchResultInterface
    {
        $this->initializeSentinelHead($startingNode);

        $allVisitedNodesStack = new VisitedNodesStack();
        $topRightSkipNodesFromEachLayerStack = new VisitedNodesStack();

        $currentNode = $this->sentinelHead;

        while ($currentNode?->nextNode !== null) {
            $allVisitedNodesStack->push($currentNode);

            $isGreaterOrEqualToCurrentNode = $this->comparator->compare($data, $currentNode->data) !== -1;
            $isLessThanOrEqualToNextNode = $this->comparator->compare($data, $currentNode->nextNode?->data) !== 1;

            if ($isGreaterOrEqualToCurrentNode && $isLessThanOrEqualToNextNode) {
                if ($currentNode instanceof SkipNode) {
                    $topRightSkipNodesFromEachLayerStack->push($currentNode);
                    $currentNode = $currentNode->nextLayerNode;
                    continue;
                } else {
                    return new SkipListResult($currentNode, $allVisitedNodesStack, $topRightSkipNodesFromEachLayerStack);
                }
            }

            $currentNode = $currentNode->nextNode;
        }

        throw new \LogicException('Some node must be returned - the value must fit between sentinels.');
    }

    public function insertAuxiliaryNodes(VisitedNodesStack $visitedSkipNodesStack, Node $newlyInsertedNode): void
    {
        $this->skipNodeFactory->createSkipNodeInMultipleLayers(
            $newlyInsertedNode,
            $visitedSkipNodesStack,
        );
    }

    private function initializeSentinelHead(Node $startingNode): void
    {
        if (isset($this->sentinelHead) === false) {
            // in case only search is tested, starting node is already created skip sentinel
            if ($startingNode instanceof SkipNode) {
                $this->sentinelHead = $startingNode;
            } else {
                $this->sentinelHead = $this->skipNodeFactory->createSkipSentinelsInAllLayers($startingNode);
            }
        }
    }
}
