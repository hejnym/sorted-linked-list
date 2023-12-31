# Sorted linked list

Customizable implementation of sorted linked list in PHP. Linked lists offer dynamic size adjustment, efficient 
insertion and deletion operations, flexible memory allocation without the need for contiguous memory, and simplified 
implementation for certain operations compared to arrays, making them well-suited for scenarios requiring frequent 
changes in size or frequent insertions and deletions.

Linked lists have a disadvantage of slower access time (O(n)) compared to arrays (O(1)). Linear search checks elements 
sequentially, taking O(n) time in the worst case, where n is the number of elements. This issue can be tackled by 
utilizing other data structures like skip list or binary tree which can greatly improve it.

Skip lists, utilizing a hierarchical structure with multiple levels of pointers, offer an average-case search time 
complexity of O(log n), making them more efficient for searches, especially in larger datasets.

Iterator pattern is implemented so that each loop can be used independently and list operations can be done during a
loop (without locks).

## Usage

```php
$layerResolver = new \Mano\SortedLinkedList\Search\SkipList\LayerResolver();
$skipNodeFactory = new \Mano\SortedLinkedList\Search\SkipList\SkipNodeFactory($layerResolver);
// your own comparator can be used to evaluate objects or any other forms
$comparator = new \Mano\SortedLinkedList\Comparator\Alphanumerical()
$search = new \Mano\SortedLinkedList\Search\SkipList\SkipList($comparator, $skipNodeFactory);

$list = new SortedLinkedList($skipList);

$list->push(5);
$list->push(1);
$list->push(3);
$list->push(8);
$list->push(2);

foreach ($list as $item) {
    echo $item;

    $list->push(0); // this will not affect the iteration as the value is pushed before pointer
}

// output: 1, 2, 3, 5, 8 
```