<?php

use eftec\bladeone\BladeOne;

class View
{
    protected static $blade;

    public static function render($view, $data = [])
    {
        if (!self::$blade) {
            $views = VIEW_PATH;
            $cache = STORAGE_PATH . '/cache';

            self::$blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
        }

        echo self::$blade->run($view, $data);
    }
}