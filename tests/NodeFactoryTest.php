<?php

declare(strict_types=1);

use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\NodeFactory;
use PHPUnit\Framework\TestCase;

class NodeFactoryTest extends TestCase
{
    private NodeFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new NodeFactory();
    }

    public function testCreateHeadNodeFromArray(): void
    {
        $headNode = $this->factory->createHeadNodeFromArray([5,'2',4,'1']);

        $fourthExpectedNode = new Node(5, new Node(INF, null));
        $thirdExpectedNode = new Node(4, $fourthExpectedNode);
        $secondExpectedNode = new Node('2', $thirdExpectedNode);
        $firstExpectedNode = new Node('1', $secondExpectedNode);
        $sentinel = new Node(-INF, $firstExpectedNode);

        $this->assertEquals($sentinel, $headNode);
    }

    public function testCreateAfterNode(): void
    {
        $secondNode = new Node(4, null);
        $firstNode = new Node('2', $secondNode);

        $newNode = $this->factory->createAfterNode($firstNode, '3');

        $this->assertSame('3', $newNode->data);
        $this->assertSame($firstNode->nextNode, $newNode);
        $this->assertSame($newNode->nextNode, $secondNode);
    }

    public function testCreateAfterNodeWhenLast(): void
    {
        $secondNode = new Node(4, null);
        $firstNode = new Node('2', $secondNode);

        $newNode = $this->factory->createAfterNode($secondNode, '3');

        $this->assertSame('3', $newNode->data);
        $this->assertSame($firstNode->nextNode, $secondNode);
        $this->assertSame($secondNode->nextNode, $newNode);
    }
}
