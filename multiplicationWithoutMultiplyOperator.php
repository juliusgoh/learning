<?php

function multiplyWithoutOperator($num1, $num2)
{
    // Handle negative numbers
    $negative = false;
    if ($num1 < 0 && $num2 > 0 || $num1 > 0 && $num2 < 0)
    {
        $negative = true;
    }

    // Perform multiplication using recursion
    $num1 = abs($num1);
    $num2 = abs($num2);

    $result = multiplyRecursive($num1, $num2);

    // Handle negative result
    if ($negative)
    {
        $result = -$result;
    }

    return $result;
}

function multiplyRecursive($num1, $num2)
{
    if ($num2 === 0)
    {
        return 0;
    }

    return $num1 + multiplyRecursive($num1, $num2 - 1);
}

// Example usage
$num1 = -5;
$num2 = -4;
$product = multiplyWithoutOperator($num1, $num2);
echo "Product: " . $product;  // Output: Product: 20
