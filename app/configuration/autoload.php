<?php
    function autoload($className) {
        if (file_exists(__DIR__.'/../classes/'.$className.'.php')) { // main classes
            require_once __DIR__.'/../classes/'.$className.'.php';
        } else {
            throw new Exception("Could not find class file!", 1);
        }
    }

    spl_autoload_register("autoload");