<?php
# Method Overloading
echo "**** Method overloading ****\n";
class Shape
{
    const PI = 3.142;
    function __call($name, $arg)
    {
        if ($name == 'area')
            switch (count($arg))
            {
                case 0:
                    return 0;
                case 1:
                    return self::PI * $arg[0];
                case 2:
                    return $arg[0] * $arg[1];
            }
    }
}
$circle = new Shape();
echo $circle->area(3) . "\n";
$rect = new Shape();
echo $rect->area(8, 6) . "\n\n";

#Method Override
echo "**** Method override ****\n";
class Robot
{
    public function greet()
    {
        return 'Hello!';
    }
}

class Android extends Robot
{
    public function greet()
    {
        return 'Hi';
    }
}

$robot = new Robot();

echo $robot->greet()."\n"; // Hello

$android = new Android();
echo $android->greet()."\n\n"; // Hi!