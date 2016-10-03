<?php

class Input {

    public static function isExist($type = 'post') {
        switch ($type) {
            case 'post':
                return (!empty($_POST)) ? true : false;
            case 'get':
                return (!empty($_GET)) ? true : false;
            default:
                return false;
        }
    }

    public static function get($value) {
        if (isset($_POST[$value])) {
            return $_POST[$value];
        } else if (isset($_GET[$value])) {
            return $_GET[$value];
        }
    }
    
}
