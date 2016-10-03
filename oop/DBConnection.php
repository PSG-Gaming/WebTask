<?php
require_once 'Configuration.php';

class DBConnection {

       private static $_instance = null;
       private $_pdo = null;

       private function __construct() {
        try {
            
            $this->_pdo = new PDO("mysql:host=" . Configuration::get('server') . "; dbname=" . Configuration::get('database') . "", Configuration::get('username'), Configuration::get('password'));
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
        } catch (PDOException $exc) {
            
            die($exc->getMessage());
            
        }
    }
                
    public static function getInstance() {
        
        if (!isset(self::$_instance)) {
            self::$_instance = new DBConnection();
        } 
        return self::$_instance->_pdo;
    }
    
}
