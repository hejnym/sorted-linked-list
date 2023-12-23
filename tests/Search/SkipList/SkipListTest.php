<?php

namespace Search\SkipList;

use Mano\SortedLinkedList\Comparator\Alphanumeric;
use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\Search\SkipList\SkipList;
use Mano\SortedLinkedList\Search\SkipList\SkipListResult;
use Mano\SortedLinkedList\Search\SkipList\SkipNode;
use Mano\SortedLinkedList\Search\SkipList\VisitedNodesStack;
use PHPUnit\Framework\TestCase;

class SkipListTest extends TestCase
{
    private Node $tailSentinel;
    private Node $nineNode;
    private Node $eightNode;
    private Node $sixNode;
    private Node $threeNode;
    private Node $twoNode;
    private Node $oneNode;
    private Node $headSentinel;

    private SkipNode $tailSkipNode;
    private SkipNode $nineSkipNode;
    private SkipNode $threeSkipNode;
    private SkipNode $headSkipNode;

    protected function setUp(): void
    {
        /**
         * -INF ---------------- 3 --------------- 9 --- INF
         *   |                   |                 |      |
         * -INF --- 1 ---- 2 --- 3 --- 6 --- 8 --- 9 --- INF
         */

        $this->tailSentinel = new Node(INF, null);
        $this->nineNode = new Node(8, $this->tailSentinel);
        $this->eightNode = new Node(8, $this->nineNode);
        $this->sixNode = new Node(6, $this->eightNode);
        $this->threeNode = new Node(3, $this->sixNode);
        $this->twoNode = new Node(2, $this->threeNode);
        $this->oneNode = new Node(1, $this->twoNode);
        $this->headSentinel = new Node(-INF, $this->oneNode);

        $this->tailSkipNode = new SkipNode(INF, $this->tailSentinel, null);
        $this->nineSkipNode = new SkipNode(9, $this->nineNode, $this->tailSkipNode);
        $this->threeSkipNode = new SkipNode(3, $this->threeNode, $this->nineSkipNode);
        $this->headSkipNode = new SkipNode(-INF, $this->headSentinel, $this->threeSkipNode);
    }

    public function testEmpty(): void
    {
        $tail = new Node(INF, null);
        $head = new Node(-INF, $tail);

        $skipTail = new SkipNode(INF, $tail, null);
        $this->headSkipNode = new SkipNode(-INF, $head, $skipTail);

        $this->assertSkipListResult(
            2,
            $head,
            new VisitedNodesStack($this->headSkipNode, $head)
        );
    }

    public function testResultAfterMiddleSkipNode(): void
    {
        $this->assertSkipListResult(
            '8',
            $this->sixNode,
            new VisitedNodesStack($this->headSkipNode, $this->threeSkipNode, $this->threeNode, $this->sixNode)
        );

        $this->assertSkipListResult(
            '6',
            $this->threeNode,
            new VisitedNodesStack($this->headSkipNode, $this->threeSkipNode, $this->threeNode)
        );
    }

    public function testResultBeforeMiddleSkipNode(): void
    {
        $this->assertSkipListResult(
            '1',
            $this->headSentinel,
            new VisitedNodesStack($this->headSkipNode, $this->headSentinel)
        );

        $this->assertSkipListResult(
            '3',
            $this->twoNode,
            new VisitedNodesStack($this->headSkipNode, $this->headSentinel, $this->oneNode, $this->twoNode)
        );
    }

    public function testMaxResult(): void
    {
        $this->assertSkipListResult(
            25,
            $this->nineNode,
            new VisitedNodesStack($this->headSkipNode, $this->threeSkipNode, $this->nineSkipNode, $this->nineNode)
        );
    }

    public function testMinResult(): void
    {
        $this->assertSkipListResult(
            -4,
            $this->headSentinel,
            new VisitedNodesStack($this->headSkipNode, $this->headSentinel)
        );
    }


    protected function assertSkipListResult(
        mixed $dataToSearch,
        Node $expectedFinalNode,
        VisitedNodesStack $expectedVisitedNodes
    ): void {
        $service = new SkipList(new Alphanumeric());

        /** @var SkipListResult $result */
        $result = $service->getNodeThatPrecedes($dataToSearch, $this->headSkipNode);

        $this->assertSame(
            $expectedFinalNode,
            $result->getNode()
        );

        $this->assertEquals(
            iterator_to_array($expectedVisitedNodes),
            iterator_to_array($result->getVisitedSkipNodesStack()),
        );
    }
}
