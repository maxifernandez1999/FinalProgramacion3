<?php
class DB_PDO
{
    private static $_objetoAccesoDatos;
    private $_objetoPDO;
 
    private function __construct($host,$user,$pass,$dataBase)
    {
        try {
            $clave = $pass != "" ? $pass : ""; 
            $this->_objetoPDO = new PDO("mysql:host=$host;dbname=$dataBase;charset=utf8", $user, $clave);
 
        } catch (PDOException $e) {
            print "Error!!!<br/>" . $e->getMessage();
            die();
        }
    }
    //PREPARA LA CONSULTA
    public function RetornarConsulta($sql)
    {
        return $this->_objetoPDO->prepare($sql);
    }
 
    public function RetornarUltimoIdInsertado()
    { 
        return $this->_objetoPDO->lastInsertId(); 
    }
    //SINGLETON
    public static function InstanciarObjetoPDO($host,$user,$pass,$dataBase)
    {
        if (!isset(self::$_objetoAccesoDatos)) {       
            self::$_objetoAccesoDatos = new DB_PDO($host,$user,$pass,$dataBase); 
        }
 
        return self::$_objetoAccesoDatos;        
    }
 
    // Evita que el objeto se pueda clonar
    public function __clone()
    {
        trigger_error('La clonaci&oacute;n de este objeto no est&aacute; permitida!!!', E_USER_ERROR);
    }
}
