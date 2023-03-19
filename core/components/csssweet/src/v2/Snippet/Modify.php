<?php
namespace CssSweet\v2\Snippet;

use CssSweet\v2\Traits\Modifier;

class Modify extends Snippet
{
    use Modifier;
    public function modify($input, $options)
    {
        // Get input: grab the first float in the string, then clean it for the unit
        $inputValue = floatval(trim($input));
        $unit = preg_replace('/[^a-zA-Z]/', '', trim($input, $inputValue));

        // Get options: operators go in an array, extract remaining float
        if (empty($options)) {
            return $inputValue . $unit;
        }
        preg_match('/[\+\-\*\/]/', $options, $op);
        $options = preg_replace('/[\+\-\*\/]/', '', $options);
        $optionValue = floatval(trim($options));

        // Only first operator
        $op = (empty($op[0])) ? '+' : $op[0];

        // Simple math only
        switch ($op) {
            case '-':
                $val = $inputValue - $optionValue;
                break;
            case '*':
                $val = $inputValue * $optionValue;
                break;
            case '/':
                $val = $inputValue / $optionValue;
                break;
            case '+':
            default:
                $val = $inputValue + $optionValue;
                break;
        }

        // Results
        return $val . $unit;
    }
}