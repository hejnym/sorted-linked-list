<?php

declare(strict_types=1);

use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\NodeFactory;
use Mano\SortedLinkedList\SortedLinkedList;
use PHPUnit\Framework\TestCase;

class SortedLinkedListIntegrationTest extends TestCase
{
    private NodeFactory $factory;
    private SortedLinkedList $list;

    protected function setUp(): void
    {
        $this->factory = new NodeFactory();
        $this->list = new SortedLinkedList($this->factory);
    }

    public function testCreateFromArray(): void
    {
        $expectedHead = new Node(
            'first',
            new Node('second', null)
        );

        $this->list->createFromArray(['first', 'second']);

        $this->assertEquals($expectedHead, $this->list->current());
        $this->list->next();
        $this->assertEquals($expectedHead->nextNode, $this->list->current());
    }


    public function testPushToEmptyList(): void
    {
        $this->assertTrue($this->list->isEmpty());
        $this->list->push('2');
        $this->assertFalse($this->list->isEmpty());

        $this->assertSame(['2'], iterator_to_array($this->list->getData()));
    }

    /**
     * @dataProvider provideMultipleVariants
     * @param array<int|string> $currentList
     * @param array<int|string> $expectedData
     */
    public function testPushMultipleVariants(array $currentList, int|string $newData, array $expectedData): void
    {
        $this->list->createFromArray($currentList);

        $this->list->push($newData);

        $this->assertSame($expectedData, iterator_to_array($this->list->getData()));
    }

    public static function provideMultipleVariants(): Iterator
    {
        // $currentList, $newData, $expectedData
        yield  'push to first place' => [[8, '3'], '2', ['2', '3', 8]];
        yield  'push to last place' => [[3, '1'], '5', ['1', 3, '5']];
        yield  'push to equal place' => [[1, '3', 3, 5], 3 , [1, 3, 3, '3', 5]];
        yield  'push to alphabet' => [['a', 'c', 'd'], 'b' , ['a', 'b', 'c', 'd']];
        yield  'push to alphanumeric' => [[1, 'b', '4'], '5' , [1, '4', '5', 'b']];
        yield  'test edge cases' => [[1, 4, 5, 5], '5' , [1, 4, '5', 5, 5]];
    }
}
