<?php

/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 28/10/2017
 * Time: 01:21
 */
class BinaryHeap
{
    protected $heap;

    public function __construct() {
        $this->heap  = array();
    }

    public function isEmpty() {
        return empty($this->heap);
    }

    public function count() {
        // returns the heapsize
        return count($this->heap) - 1;
    }

    public function extract() {
        if ($this->isEmpty()) {
            throw new RunTimeException('Heap is empty');
        }

        // extract the root item
        $root = array_shift($this->heap);

        if (!$this->isEmpty()) {
            // move last item into the root so the heap is
            // no longer disjointed
            $last = array_pop($this->heap);
            array_unshift($this->heap, $last);

            // transform semiheap to heap
            $this->adjust(0);
        }

        return $root;
    }

    public function compare($item1, $item2) {
        if ($item1['distance'] === $item2['distance']) {
            return 0;
        }
        // reverse the comparison to change to a MinHeap!
        return ($item1['distance'] < $item2['distance'] ? 1 : -1);
    }

    protected function isLeaf($node) {
        // there will always be 2n + 1 nodes in the
        // sub-heap
        return ((2 * $node) + 1) > $this->count();
    }

    protected function adjust($root) {
        // we've gone as far as we can down the tree if
        // root is a leaf
        if (!$this->isLeaf($root)) {
            $left  = (2 * $root) + 1; // left child
            $right = (2 * $root) + 2; // right child

            // if root is less than either of its children
            $h = $this->heap;
            if (
                (isset($h[$left]) &&
                    $this->compare($h[$root], $h[$left]) < 0)
                || (isset($h[$right]) &&
                    $this->compare($h[$root], $h[$right]) < 0)
            ) {
                // find the larger child
                if (isset($h[$left]) && isset($h[$right])) {
                    $j = ($this->compare($h[$left], $h[$right]) >= 0)
                        ? $left : $right;
                }
                else if (isset($h[$left])) {
                    $j = $left; // left child only
                }
                else {
                    $j = $right; // right child only
                }

                // swap places with root
                list($this->heap[$root], $this->heap[$j]) =
                    array($this->heap[$j], $this->heap[$root]);

                // recursively adjust semiheap rooted at new
                // node j
                $this->adjust($j);
            }
        }
    }
    public function insert($item) {
        // insert new items at the bottom of the heap
        $this->heap[] = $item;

        // trickle up to the correct location
        $place = $this->count();
        $parent = floor($place / 2);
        // while not at root and greater than parent
        while (
            $place > 0 && $this->compare(
                $this->heap[$place], $this->heap[$parent]) >= 0
        ) {
            // swap places
            list($this->heap[$place], $this->heap[$parent]) =
                array($this->heap[$parent], $this->heap[$place]);
            $place = $parent;
            $parent = floor($place / 2);
        }
    }
    public function heapToString(){
        $insideImplode=array();
        foreach($this->heap as $item){
            $insideImplode[]=implode(";",$item);
        }
        return implode(",",$insideImplode);
    }
    public function stringToHeap($string){
        $mainExplode=explode(",",$string);
        $this->heap=array();
        foreach($mainExplode as $item){
            $item=explode(";",$item);
            $this->heap[]=["capteur"=>$item[0],"distance"=>$item[1]];
        }
    }
}

$heap= new BinaryHeap();
$heap->insert(["capteur"=>2,"distance"=>13]);
$heap->insert(["capteur"=>200,"distance"=>200]);
$heap->insert(["capteur"=>100,"distance"=>100]);
$var=$heap->heapToString();
echo $var."<br/>";
var_dump( $heap->extract());

$hea=new BinaryHeap();
$hea->stringToHeap($var);
echo "<br/>VOILA<br/>".$hea->heapToString();
?>