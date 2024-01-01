<?php

declare(strict_types=1);

use Mano\SortedLinkedList\Comparator\Alphanumeric;
use Mano\SortedLinkedList\Search\LinearSearch\LinearSearch;
use Mano\SortedLinkedList\Search\SkipList\LayerResolver;
use Mano\SortedLinkedList\Search\SkipList\SkipList;
use Mano\SortedLinkedList\Search\SkipList\SkipListResult;
use Mano\SortedLinkedList\Search\SkipList\SkipNodeFactory;
use Mano\SortedLinkedList\SortedLinkedList;
use PHPUnit\Framework\TestCase;

class SortedLinkedListIntegrationTest extends TestCase
{
    private SortedLinkedList $listWithLinearSearch;
    private SortedLinkedList $listWithSkipList;
    private LinearSearch $linearSearch;
    private SkipList $skipList;
    /** @var SortedLinkedList[] */
    private array $bothLists;

    protected function setUp(): void
    {
        $comparator = new Alphanumeric();

        // skip list
        $layerResolver = new LayerResolver(0.25, 5);
        $skipNodeFactory = new SkipNodeFactory($layerResolver);
        $this->skipList = new SkipList($comparator, $skipNodeFactory);

        // linear search
        $this->linearSearch = new LinearSearch($comparator);

        $this->listWithLinearSearch = new SortedLinkedList($this->linearSearch);
        $this->listWithSkipList = new SortedLinkedList($this->skipList);

        $this->bothLists = [$this->listWithLinearSearch, $this->listWithSkipList];
    }


    public function testPushToEmptyList(): void
    {
        foreach ($this->bothLists as $list) {
            $this->assertTrue($list->isEmpty());
            $list->push('2');
            $this->assertFalse($list->isEmpty());

            $this->assertSame(['2'], iterator_to_array($list));
        }
    }

    /**
     * @dataProvider provideMultipleVariants
     * @param array<mixed> $itemsToBePushed
     * @param array<mixed> $expectedData
     */
    public function testPushMultipleVariants(array $itemsToBePushed, array $expectedData): void
    {
        foreach ($this->bothLists as $list) {
            foreach ($itemsToBePushed as $item) {
                $list->push($item);
            }

            $this->assertSame($expectedData, iterator_to_array($list));
        }
    }

    public static function provideMultipleVariants(): Iterator
    {
        // $itemsToBePushed, $expectedData
        yield  'push to first place' => [[8, '3','2'], ['2', '3', 8]];
        yield  'push to last place' => [[3, '1','5'], ['1', 3, '5']];
        yield  'push to equal place' => [[1, '3', 3, 5,  3 ], [1, 3, 3, '3', 5]];
        yield  'push to alphabet' => [['a', 'c', 'd', 'b'] , ['a', 'b', 'c', 'd']];
        yield  'push to alphanumeric' => [[1, 'b', '4', '5'] , [1, '4', '5', 'b']];
        yield  'test edge cases' => [[1, 4, 5, 5, '5'],  [1, 4, '5', 5, 5]];
    }

    /**
     * @dataProvider provideLoops
     */
    public function testPushDuringLoop(int $valuePushed, int $atLoop, int $loopsCount): void
    {
        foreach ($this->bothLists as $list) {
            $list->push(1);
            $list->push(3);
            $list->push(5);

            $i = 0;
            foreach ($list as $move) {
                $i++;

                if($i === $atLoop) {
                    $list->push($valuePushed);
                }
            }

            $this->assertSame($loopsCount, $i);
        }
    }

    public static function provideLoops(): Iterator
    {
        // int $valuePushed, int $atLoop, int $loopsCount
        yield  'push before current pointer will not affect loop' => [1, 3, 3];
        yield  'push before current pointer will not affect loop #2' => [2, 2, 3];
        yield  'after before current pointer will affect loop' => [4, 2, 4];
    }

    /**
     * @dataProvider provideSearchValues
     */
    public function testSearch(mixed $valueSearched, mixed $expectedResult): void
    {
        foreach ($this->bothLists as $list) {
            $list->push(1);
            $list->push(3);
            $list->push(5);

            $this->assertSame(
                $expectedResult,
                $list->find($valueSearched)?->data
            );
        }
    }

    public static function provideSearchValues(): Iterator
    {
        // int|string $valueSearched, bool $found
        yield   [2, null];
        yield   [3, 3];
        yield   [5, 5];
        yield   [6, null];
    }

    public function testSkipListEffectivity(): void
    {
        $list = $this->listWithSkipList;

        $list->push(1);
        $item1 =  $list->find(1);
        $this->assertNotNull($item1);

        for ($i = 1; $i < 1000; $i++) {
            $list->push(rand(2, 999));
        }

        $list->push(1000);


        /** @var SkipListResult $result */
        $result = $this->skipList->getNodeThatPrecedes(1000, $item1);

        $this->assertSame(1000, $result->getNode()->nextNode?->data);

        $this->assertLessThan(
            50, // average is around 20, this value is exaggerated for edge cases
            $result->getAllVisitedNodesStack()->count()
        );
    }
}
