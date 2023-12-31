<?php

namespace Search\SkipList;

use Mano\SortedLinkedList\Comparator\Alphanumeric;
use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\Search\SkipList\LayerResolver;
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

    private SkipNode $tailSkipNodeLayer1;
    private SkipNode $nineSkipNodeLayer1;
    private SkipNode $threeSkipNodeLayer1;
    private SkipNode $headSkipNodeLayer1;

    private SkipNode $tailSkipNodeLayer2;
    private SkipNode $nineSkipNodeLayer2;
    private SkipNode $headSkipNodeLayer2;

    /**
     * @var Stub&LayerResolver
     */
    private $coinFlipper;

    private SkipList $skipList;

    protected function setUp(): void
    {
        /**
         * -INF ---------------------------------- 9 --- INF
         *   |                                     |      |
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

        $this->tailSkipNodeLayer1 = new SkipNode($this->tailSentinel, null);
        $this->nineSkipNodeLayer1 = new SkipNode($this->nineNode, $this->tailSkipNodeLayer1);
        $this->threeSkipNodeLayer1 = new SkipNode($this->threeNode, $this->nineSkipNodeLayer1);
        $this->headSkipNodeLayer1 = new SkipNode($this->headSentinel, $this->threeSkipNodeLayer1);

        $this->tailSkipNodeLayer2 = new SkipNode($this->tailSkipNodeLayer1, null);
        $this->nineSkipNodeLayer2 = new SkipNode($this->nineSkipNodeLayer1, $this->tailSkipNodeLayer2);
        $this->headSkipNodeLayer2 = new SkipNode($this->headSkipNodeLayer1, $this->nineSkipNodeLayer2);

        $this->coinFlipper = $this->createStub(LayerResolver::class);
        $this->coinFlipper->method('howManyLayersToSpan')->willReturn(1);

        $this->skipList = new SkipList(new Alphanumeric(), new SkipNodeFactory($this->coinFlipper));
    }

    public function testSearchEmpty(): void
    {
        $tail = new Node(INF, null);
        $head = new Node(-INF, $tail);

        $skipTail = new SkipNode($tail, null);
        $this->headSkipNodeLayer1 = new SkipNode($head, $skipTail);
        $skipTailLayer2 = new SkipNode($skipTail, null);
        $this->headSkipNodeLayer2 = new SkipNode($this->headSkipNodeLayer1, $skipTailLayer2);

        $this->assertSkipListSearch(
            2,
            $head,
            new VisitedNodesStack($this->headSkipNodeLayer2, $this->headSkipNodeLayer1, $head)
        );
    }

    public function testSearchAfterMiddleSkipNode(): void
    {
        $this->assertSkipListSearch(
            '8',
            $this->sixNode,
            new VisitedNodesStack($this->headSkipNodeLayer2, $this->headSkipNodeLayer1, $this->threeSkipNodeLayer1, $this->threeNode, $this->sixNode)
        );

        $this->assertSkipListSearch(
            '6',
            $this->threeNode,
            new VisitedNodesStack($this->headSkipNodeLayer2, $this->headSkipNodeLayer1, $this->threeSkipNodeLayer1, $this->threeNode)
        );
    }

    public function testSearchBeforeMiddleSkipNode(): void
    {
        $this->assertSkipListSearch(
            '1',
            $this->headSentinel,
            new VisitedNodesStack($this->headSkipNodeLayer2, $this->headSkipNodeLayer1, $this->headSentinel)
        );

        $this->assertSkipListSearch(
            '3',
            $this->twoNode,
            new VisitedNodesStack($this->headSkipNodeLayer2, $this->headSkipNodeLayer1, $this->headSentinel, $this->oneNode, $this->twoNode)
        );
    }

    public function testSearchMin(): void
    {
        $this->assertSkipListSearch(
            -4,
            $this->headSentinel,
            new VisitedNodesStack($this->headSkipNodeLayer2, $this->headSkipNodeLayer1, $this->headSentinel)
        );
    }

    public function testSearchMax(): void
    {
        $this->assertSkipListSearch(
            25,
            $this->nineNode,
            new VisitedNodesStack($this->headSkipNodeLayer2, $this->nineSkipNodeLayer2, $this->nineSkipNodeLayer1, $this->nineNode)
        );
    }

    public function testInsertAuxiliaryNodes(): void
    {
        $visitedStack = new VisitedNodesStack(
            $this->headSkipNodeLayer2,
            $this->headSkipNodeLayer1,
            $this->threeSkipNodeLayer1,
        );

        $newlyCreatedNode = $this->eightNode;

        $this->skipList->insertAuxiliaryNodes($visitedStack, $newlyCreatedNode);

        /** @var SkipNode $newlyCreatedSkipNode */
        $newlyCreatedSkipNode = $this->threeSkipNodeLayer1->nextNode;

        $this->assertSame($this->threeSkipNodeLayer1->nextNode, $newlyCreatedSkipNode);
        $this->assertSame($this->nineSkipNodeLayer1, $newlyCreatedSkipNode->nextNode);
        $this->assertSame($newlyCreatedNode, $newlyCreatedSkipNode->nextLayerNode);
    }


    protected function assertSkipListSearch(
        mixed $dataToSearch,
        Node $expectedFinalNode,
        VisitedNodesStack $expectedVisitedNodes
    ): void {

        /** @var SkipListResult $result */
        $result = $this->skipList->getNodeThatPrecedes($dataToSearch, $this->headSkipNodeLayer2);

        $this->assertSame(
            $expectedFinalNode,
            $result->getNode()
        );

        $this->assertEquals(
            iterator_to_array($expectedVisitedNodes),
            iterator_to_array($result->getAllVisitedNodesStack()),
        );
    }
}
