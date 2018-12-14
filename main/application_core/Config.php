<?php

class Config
{
    public static function get()
    {
        $s = DIRECTORY_SEPARATOR;
        $path = ".{$s}main{$s}config{$s}main.ini";
        return parse_ini_file($path, true);
    }
}