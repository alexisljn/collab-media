<?php

/**
 * Debug function
 * d($var);
 */
function d($var,$caller=null)
{
    if(!isset($caller)){
        $a = debug_backtrace(1);
        $caller = array_shift($a);
    }
    echo '<code>File: '.$caller['file'].' / Line: '.$caller['line'].'</code>';
    echo '<pre>';
    yii\helpers\VarDumper::dump($var, 20, true);
    echo '</pre>';
}

/**
 * Debug function with die() after
 * dd($var);
 */
function dd($var)
{
    $a = debug_backtrace(1);
    $caller = array_shift($a);
    d($var,$caller);
    die();
}