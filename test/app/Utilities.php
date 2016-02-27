<?php namespace Test;

function cloneArray(array $arr)
{
    $cloned = [];
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            $cloned[$key] = cloneArray($value);
        } elseif (is_object($value)) {
            $cloned[$key] = clone $value;
        } else {
            $cloned[$key] = $value;
        }
    }
    
    return $cloned;
}
