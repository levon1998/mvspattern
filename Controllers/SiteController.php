<?php

class SiteController
{
    public function index()
    {
        $string = '11-05-1998';

        $pattern = '/([0-9]{2})-([0-9]{2})-([0-9]{4})/';

        $replace = 'year $3, mount $2, day $1';

//        echo preg_replace($pattern, $replace, $string);

    }
}