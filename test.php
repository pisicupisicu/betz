<?php

/*
 * Test comments
 * 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$numbers = array(1,3,2,5,2);
$array_size = count($numbers);

echo "Numbers before sort: ";
for ( $i = 0; $i < $array_size; $i++ )
   echo $numbers[$i];
echo "<br/>";

for ( $i = 0; $i < $array_size; $i++ )
{
   for ($j = 0; $j < $array_size; $j++ )
   {
      if ($numbers[$i] < $numbers[$j])
      {
         $temp = $numbers[$i];
         $numbers[$i] = $numbers[$j];
         $numbers[$j] = $temp;
         
         echo "TEMP $temp $numbers[$i] $numbers[$j]<br/>";
         for ( $k = 0; $k < $array_size; $k++ )
            echo $numbers[$k];
         echo '<br/>';
      }
   }
}


echo "Numbers after sort: ";
for( $i = 0; $i < $array_size; $i++ )
   echo $numbers[$i];
echo "<br/>";



function bubbleSort ($items) 
{ 
        $size = count($items); 
        for ($i=0; $i<$size; $i++) {
             $flag = 0;
             for ($j=0; $j<$size-1-$i; $j++) { 
                  if ($items[$j+1] < $items[$j]) { 
                      arraySwap($items, $j, $j+1);
                      $flag = 1;
                  } 
             }
             if(!$flag) break;
        } 
        return $items; 
}
    
function arraySwap (&$arr, $index1, $index2) 
{ 
    list($arr[$index1], $arr[$index2]) = array($arr[$index2], $arr[$index1]); 
}

$items = array(1,4,5,7,2,6,3);
$items = bubbleSort($items);
print '<pre>';
print_r($items);

$a = 'a';
$A = 'A';

echo $a.' '.$A.'<br/>';

function abc()
{
    echo 'abc';
}

echo 'Sublime rullz 222';

abc();
ABC();
?>
