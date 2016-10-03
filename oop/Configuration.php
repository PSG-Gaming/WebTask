<?php

abstract class Configuration {

    private static $config = array(
        "server" => '127.0.0.1',
        "database" => 'notes',
        "username" => 'notes',
        "password" => 'pass'
    );

    public static function get($param) {
        return self::$config[$param];
    }
    
}
