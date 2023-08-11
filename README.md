# Sorted linked list

Simple implementation of sorted linked list in PHP.

The point is to have a list that is always ordered. In order not to reorder the list after every push to the list, only
new element is compared against the current items of the list and placed accordingly. 

Uses iterator pattern so each loop can be used independently and list operations can be done during the loop 
(without locks).

There are some ideas to speed the process up for large lists:
- we can add more pointers to the list (half of it) so that we know which part of the list must be traversed
- use doubly linked list and have a reference to the tail so that we can search from both directions in parallel 

## Usage

```php
$list = new \Mano\SortedLinkedList\SortedLinkedList(
    new \Mano\SortedLinkedList\NodeFactory()
);

// either create initially from array
$list->createFromArray([3, 8, 1]);

// or create by pushing element by element
$list->push('8');
$list->push(10);


foreach ($list as $item) {
    echo $item;
    $list->push(1);
}
```
