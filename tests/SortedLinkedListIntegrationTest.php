<?php

declare(strict_types=1);

use Mano\SortedLinkedList\Comparator\Alphanumeric;
use Mano\SortedLinkedList\Node;
use Mano\SortedLinkedList\Search\LinearSearch\LinearSearch;
use Mano\SortedLinkedList\Search\SkipList\LayerResolver;
use Mano\SortedLinkedList\Search\SkipList\SkipList;
use Mano\SortedLinkedList\Search\SkipList\SkipListResult;
use Mano\SortedLinkedList\Search\SkipList\SkipNodeFactory;
use Mano\SortedLinkedList\Search\TraceableResultInterface;
use Mano\SortedLinkedList\SortedLinkedList;
use PHPUnit\Framework\TestCase;

class SortedLinkedListIntegrationTest extends TestCase
{
    private SortedLinkedList $list;
    private LinearSearch $search;

    protected function setUp(): void
    {
        $this->search = new LinearSearch(new Alphanumeric());
        $this->list = new SortedLinkedList($this->search);
    }

    public function testCreateFromArray(): void
    {
        $expectedHead = new Node(
            'first',
            new Node('second', null)
        );

        $this->list->createFromArray(['first', 'second']);

        $iterator = $this->list->getIterator();

        $this->assertEquals($expectedHead->data, $iterator->current());
        $iterator->next();
        $this->assertEquals($expectedHead->nextNode?->data, $iterator->current());
    }


    public function testPushToEmptyList(): void
    {
        $this->assertTrue($this->list->isEmpty());
        $this->list->push('2');
        $this->assertFalse($this->list->isEmpty());

        $this->assertSame(['2'], iterator_to_array($this->list));
    }

    /**
     * @dataProvider provideMultipleVariants
     * @param array<mixed> $currentList
     * @param array<mixed> $expectedData
     */
    public function testPushMultipleVariants(array $currentList, mixed $newData, array $expectedData): void
    {
        $this->list->createFromArray($currentList);

        $this->list->push($newData);

        $this->assertSame($expectedData, iterator_to_array($this->list));
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

    /**
     * @dataProvider provideLoops
     */
    public function testPushDuringLoop(int $valuePushed, int $atLoop, int $loopsCount): void
    {
        $this->list->createFromArray([1, 3, 5]);

        $i = 0;
        foreach ($this->list as $move) {
            $i++;

            if($i === $atLoop) {
                $this->list->push($valuePushed); // TODO: change
            }
        }

        $this->assertSame($loopsCount, $i);
    }

    public static function provideLoops(): Iterator
    {
        // int $valuePushed, int $atLoop, int $loopsCount
        yield  'push before current pointer will not affect loop' => [1, 3, 3];
        yield  'push before current pointer will not affect loop #2' => [2, 2, 3];
        yield  'after before current pointer will affect loop' => [4, 2, 4];
    }

    /**
     * @dataProvider provideSearch
     */
    public function testSearch(mixed $valueSearched, mixed $expectedResult): void
    {
        $this->list->createFromArray([1, 3, 5]);

        $this->assertSame(
            $expectedResult,
            $this->list->find($valueSearched)?->data
        );
    }

    public static function provideSearch(): Iterator
    {
        // int|string $valueSearched, bool $found
        yield   [2, null];
        yield   [3, 3];
        yield   [5, 5];
        yield   [6, null];
    }

    public function testSkipList(): void
    {
        $layerResolver = new LayerResolver(0.2, 5);
        $skipNodeFactory = new SkipNodeFactory($layerResolver);
        $comparator = new Alphanumeric();
        $skipList = new SkipList($comparator, $skipNodeFactory, $layerResolver);
        $list = new SortedLinkedList($skipList);

        $list->push(1);
        $item1 =  $list->find(1);
        $this->assertNotNull($item1);

        for ($i = 1; $i < 1000; $i++) {
            $list->push(rand(2, 999));
        }

        $list->push(1000);


        /** @var SkipListResult $result */
        $result = $skipList->getNodeThatPrecedes(1000, $item1);

        $this->assertSame(1000, $result->getNode()->nextNode?->data);

        $this->assertLessThan(
            50,
            $result->getVisitedSkipNodesStack()->count()
        );
    }
}
