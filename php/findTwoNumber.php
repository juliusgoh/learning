<?php
function find_two_numbers($arr, $K)
{
    sort($arr); // sort the array in non-decreasing order
    $left = 0;
    $right = count($arr) - 1;
    print_r($arr);
    while ($left < $right)
    {
        echo "$left - $right - " . $arr[$left] . " - " . $arr[$right] . " - " . ($arr[$left] + $arr[$right]) . " - $K \n";
        if ($arr[$left] + $arr[$right] == $K)
        {
            return array($arr[$left], $arr[$right]); // return the two numbers that sum up to K
        }
        elseif ($arr[$left] + $arr[$right] < $K)
        {
            echo "MOVE LEFT\n";
            $left++; // move the left pointer one position to the right
        }
        else
        {
            $right--; // move the right pointer one position to the left
        }
    }
    return null; // if we reach the end of the array and have not found two numbers that sum up to K, return null
}

// Example 1
$arr1 = array(2, 7, 11, 15, 3, 20, 30, 1, 4);
$K1 = 9;
$result1 = find_two_numbers($arr1, $K1);
if ($result1 !== null)
{
    echo "The two numbers that sum up to $K1 in the array " . implode(", ", $arr1) . " are " . implode(" and ", $result1) . ".\n";
}
else
{
    echo "There are no two numbers in the array " . implode(", ", $arr1) . " that sum up to $K1.\n";
}
