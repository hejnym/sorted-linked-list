<?php

namespace Iterator;

use Mano\SortedLinkedList\Iterator\NodeIterator;
use Mano\SortedLinkedList\Node;
use PHPUnit\Framework\TestCase;

class NodeIteratorTests extends TestCase
{
    private Node $sentinelHead;
    private Node $sentinelTail;
    private Node $firstItem;

    protected function setUp(): void
    {
        $this->sentinelTail = new Node(INF, null);
        $this->firstItem = new Node('first element data', $this->sentinelTail);
        $this->sentinelHead = new Node(
            - INF,
            $this->firstItem
        );
    }

    public function testHeadMustBeSentinel(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new NodeIterator(new Node('foo'));
    }

    /**
     * @dataProvider provideSkipSentinel
     */
    public function testCurrent(bool $skipSentinels): void
    {
        $nodeIterator = new NodeIterator($this->sentinelHead, $skipSentinels);

        $this->assertTrue($nodeIterator->valid());
        $this->assertSame(
            $skipSentinels ? $this->firstItem : $this->sentinelHead,
            $nodeIterator->current()
        );

        $nodeIterator->next();

        $this->assertSame(!$skipSentinels, $nodeIterator->valid());

        if($skipSentinels) {
            $this->expectException(\RuntimeException::class);
            $nodeIterator->current();
        } else {
            $this->assertSame($this->firstItem, $nodeIterator->current());
        }

        $nodeIterator->next();

        $this->assertSame(!$skipSentinels, $nodeIterator->valid());

        if($skipSentinels) {
            $this->expectException(\RuntimeException::class);
            $nodeIterator->current();
        } else {
            $this->assertSame($this->sentinelTail, $nodeIterator->current());
        }

    }


    /**
     * @dataProvider provideSkipSentinel
     */
    public function testEmpty(bool $skipSentinels): void
    {
        $nodeIterator = new NodeIterator($sentinel = new Node(-INF, new Node(INF, null)), $skipSentinels);
        $this->assertSame($skipSentinels, !$nodeIterator->valid());

        if($skipSentinels) {
            $this->expectException(\RuntimeException::class);
            $nodeIterator->current();

        } else {
            $this->assertSame(
                $sentinel,
                $nodeIterator->current()
            );
        }

    }

    /**
     * @dataProvider provideSkipSentinel
     */
    public function testLoopWithOneItem(bool $skipSentinels): void
    {
        $nodeIterator = new NodeIterator($this->sentinelHead, $skipSentinels);

        $this->assertSame(
            $skipSentinels ? [$this->firstItem] : [$this->sentinelHead, $this->firstItem, $this->sentinelTail],
            iterator_to_array($nodeIterator)
        );
    }

    /**
     * @dataProvider provideSkipSentinel
     */
    public function testLoopWithSentinelOnly(bool $skipSentinels): void
    {
        $nodeIterator = new NodeIterator($sentinel = new Node(-INF, $tail = new Node(INF, null)), $skipSentinels);
        $nodes = [];
        foreach ($nodeIterator as $node) {
            $nodes[] = $node;
        }
        $this->assertSame(
            $skipSentinels ? [] : [$sentinel, $tail],
            $nodes
        );
    }

    public function testCallNextOnNullThrows(): void
    {
        $nodeIterator = new NodeIterator(new Node(-INF, null), false);
        $nodeIterator->next();
        $this->expectException(\RuntimeException::class);
        $nodeIterator->next();
    }

    public static function provideSkipSentinel(): \Generator
    {
        yield ['Sentinels skipped' => true];
        yield ['Sentinels not skipped' => false];
    }
}
