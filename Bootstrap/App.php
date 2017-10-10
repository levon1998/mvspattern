<?php

abstract class App
{
    public static function migrationClass($name)
    {
        $html = '';
        $html .= 'class '.$name. '{ ';
        $html .= ' } ';
        return $html;
    }
}