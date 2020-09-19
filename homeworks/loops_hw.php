<?php
$numbers = array(1,2,3,3,4,5,6,6,7,8,9);

// Prints out all values
foreach($numbers as $nums){
    echo "Value: $nums";
    echo "\n";
}


foreach($numbers as $nums)
{
    // prints all even numbers by checking if the remainder after being divided by 2 is 0 (definition of even)
    if($nums % 2 == 0)
    {
        echo "This is even: $nums";
        echo "\n";
    }
}