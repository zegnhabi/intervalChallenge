<?php
namespace App\Database;

class DB{
    
    protected $_DB;
    protected $config;
    function __construct(){
        $this->initDotEnv();
        $this->_DB = $this->createPDO();
    }

    protected function initDotEnv(){
        $baseDir = __DIR__."/../../";
        $content = file_get_contents($baseDir.".env");
        $lines = explode("\n", $content);
        foreach ($lines as $i => $line) {
            $line = preg_replace('/([\r\n\t])/','', $line);                
            if(strlen($line) > 1){
                $l = explode("=",$line);
                $this->config[$l[0]] = $l[1];
            }  
        }
    }

    protected function createPDO(){
        $dsn = "mysql:host={$this->config['DB_HOST']};dbname={$this->config['DB_DATABASE']}";
        $user = $this->config['DB_USERNAME'];
        $password = $this->config['DB_PASSWORD'];
        $options = array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        ); 
        $PDO = new \PDO($dsn, $user, $password, $options);
        return $PDO;
    }

    function Query($query, $params){
        $result = false;
        try{
            $statement = $this->_DB->prepare($query);
            $statement->execute($params);
            if ($statement->rowCount() > 0){
                $result = $statement->fetchAll(\PDO::FETCH_OBJ);
            }else{
                $result = false;
            }
        }catch(\Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }
}