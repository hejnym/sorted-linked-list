<?php

namespace Search\SkipList;

use Mano\SortedLinkedList\Comparator\Alphanumeric;
use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\Search\SkipList\CoinFlipper;
use Mano\SortedLinkedList\Search\SkipList\SkipList;
use Mano\SortedLinkedList\Search\SkipList\SkipListResult;
use Mano\SortedLinkedList\Search\SkipList\SkipNode;
use Mano\SortedLinkedList\Search\SkipList\SkipNodeFactory;
use Mano\SortedLinkedList\Search\SkipList\VisitedNodesStack;
use PHPUnit\Framework\MockObject\Stub;
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

    /**
     * @var Stub&CoinFlipper
     */
    private $coinFlipper;

    private SkipList $skipList;

    protected function setUp(): void
    {
        /**
         * -INF ---------------- 3 --------------- 9 --- INF
         *   |                   |                 |      |
         * -INF --- 1 ---- 2 --- 3 --- 6 --- 8 --- 9 --- INF
         */

        $this->tailSentinel = new Node(INF, null);
        $this->nineNode = new Node(9, $this->tailSentinel);
        $this->eightNode = new Node(8, $this->nineNode);
        $this->sixNode = new Node(6, $this->eightNode);
        $this->threeNode = new Node(3, $this->sixNode);
        $this->twoNode = new Node(2, $this->threeNode);
        $this->oneNode = new Node(1, $this->twoNode);
        $this->headSentinel = new Node(-INF, $this->oneNode);

        $this->tailSkipNode = new SkipNode($this->tailSentinel, null);
        $this->nineSkipNode = new SkipNode($this->nineNode, $this->tailSkipNode);
        $this->threeSkipNode = new SkipNode($this->threeNode, $this->nineSkipNode);
        $this->headSkipNode = new SkipNode($this->headSentinel, $this->threeSkipNode);

        $this->coinFlipper = $this->createStub(CoinFlipper::class);
        $this->coinFlipper->method('isHead')->willReturn(true);

        $this->skipList = new SkipList(new Alphanumeric(), new SkipNodeFactory(), $this->coinFlipper);
    }

    public function testSearchEmpty(): void
    {
        $tail = new Node(INF, null);
        $head = new Node(-INF, $tail);

        $skipTail = new SkipNode($tail, null);
        $this->headSkipNode = new SkipNode($head, $skipTail);

        $this->assertSkipListSearch(
            2,
            $head,
            new VisitedNodesStack($this->headSkipNode, $head)
        );
    }

    public function testSearchAfterMiddleSkipNode(): void
    {
        $this->assertSkipListSearch(
            '8',
            $this->sixNode,
            new VisitedNodesStack($this->headSkipNode, $this->threeSkipNode, $this->threeNode, $this->sixNode)
        );

        $this->assertSkipListSearch(
            '6',
            $this->threeNode,
            new VisitedNodesStack($this->headSkipNode, $this->threeSkipNode, $this->threeNode)
        );
    }

    public function testSearchBeforeMiddleSkipNode(): void
    {
        $this->assertSkipListSearch(
            '1',
            $this->headSentinel,
            new VisitedNodesStack($this->headSkipNode, $this->headSentinel)
        );

        $this->assertSkipListSearch(
            '3',
            $this->twoNode,
            new VisitedNodesStack($this->headSkipNode, $this->headSentinel, $this->oneNode, $this->twoNode)
        );
    }

    public function testSearchMax(): void
    {
        $this->assertSkipListSearch(
            25,
            $this->nineNode,
            new VisitedNodesStack($this->headSkipNode, $this->threeSkipNode, $this->nineSkipNode, $this->nineNode)
        );
    }

    public function testSearchMin(): void
    {
        $this->assertSkipListSearch(
            -4,
            $this->headSentinel,
            new VisitedNodesStack($this->headSkipNode, $this->headSentinel)
        );
    }

    public function testInsertAuxiliaryNodes(): void
    {
        $visitedStack = new VisitedNodesStack(
            $this->headSkipNode,
            $this->threeSkipNode,
            $this->threeNode,
            $this->sixNode
        );

        $newlyCreatedNode = $this->eightNode;

        $this->skipList->insertAuxiliaryNodes($visitedStack, $newlyCreatedNode);

        /** @var SkipNode $newlyCreatedSkipNode */
        $newlyCreatedSkipNode = $this->threeSkipNode->nextNode;

        $this->assertSame($this->threeSkipNode->nextNode, $newlyCreatedSkipNode);
        $this->assertSame($this->nineSkipNode, $newlyCreatedSkipNode->nextNode);
        $this->assertSame($newlyCreatedNode, $newlyCreatedSkipNode->nextLayerNode);
    }


    protected function assertSkipListSearch(
        mixed $dataToSearch,
        Node $expectedFinalNode,
        VisitedNodesStack $expectedVisitedNodes
    ): void {

        /** @var SkipListResult $result */
        $result = $this->skipList->getNodeThatPrecedes($dataToSearch, $this->headSkipNode);

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
