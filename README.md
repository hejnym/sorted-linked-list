# Sorted linked list

Customizable implementation of sorted linked list in PHP. The point is to have a list that is always ordered. In order not to reorder the list after every push to the list, only new element is compared against the current items of the list and placed accordingly. 

Uses iterator pattern so each loop can be used independently and list operations can be done during the loop 
(without locks).

## Search

As insert time for linked lists is constant O(1) the focus here is to implement a decent search mechanism that would not hinder the performcance. So far only simple linear search is implemented O(N). Skip list or binary search tree can greatly improve it to O(log N) though. 

## Usage

```php
// your own comparator can be used to evaluate objects or any other forms
$comparator = new \Mano\SortedLinkedList\Comparator\Alphanumerical()
$search = new \Mano\SortedLinkedList\Search\LinearSearch\LinearSearch($comparator);

$list = new \Mano\SortedLinkedList\SortedLinkedList($search);

// either create initially from array
$list->createFromArray([5, 1, 3]);

// or create by pushing element by element
$list->push('8');
$list->push(2);

foreach ($list as $item) {
    echo $item;

    $list->push(0); // this will not affect the iteration as the value is pushed before pointer
}

// output: 1, 2, 3, 5, '8' 
```