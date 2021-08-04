<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \App\Models\Usuario as UsuarioORM;
require_once "DB_PDO.php";
require_once "Autentificadora.php";

class Criptomoneda{
    public function AgregarCripto(Request $request, Response $response, array $args){
        $objJSON = isset($request->getParsedBody()['cripto']) ? json_decode($request->getParsedBody()['cripto']) : null;
        $file = $request->getUploadedFiles();//$_FILES
        $stdclass = new stdClass();
        
        $destiny = __DIR__ . "/../fotos/";

        $nameBefore = $file['foto']->getClientFilename();
        $extension = explode(".", $nameBefore);
        $extension = array_reverse($extension); 

        $idInsert = self::Insertar($objJSON,$nameBefore);
        if($idInsert != null){
            $file['foto']->moveTo($destiny . $nameBefore);
            
            $stdclass->exito = true;
            $stdclass->mensaje = "Cripto Agregado";
            $stdclass->exito = 200;
            $response->getBody()->write(json_encode($stdclass));
        }else{
            $stdclass->exito = false;
            $stdclass->mensaje = "Error Agregado";
            $stdclass->exito = 418;
            $response->getBody()->write(json_encode($stdclass));
        }
            
        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function Insertar($obj ,$file){
        $objetoAccesoDato = DB_PDO::InstanciarObjetoPDO("localhost","root","","cripto_bd"); 
        $consulta = $objetoAccesoDato->RetornarConsulta('INSERT INTO criptomoneda(precio,nombre,foto,nacionalidad) VALUES (:precio,:nombre,:foto,:nacionalidad)');
        $consulta->bindValue(':nombre',$obj->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $file, PDO::PARAM_STR);
        $consulta->bindValue(':nacionalidad',$obj->nacionalidad, PDO::PARAM_STR);
        $consulta->bindValue(':precio',$obj->precio, PDO::PARAM_INT);
        $consulta->execute();		
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }
    public function TraerUnaCripto(Request $request, Response $response, array $args): Response {
        $ID = $args['id'];
		$stdclass =new stdClass();
		$cripto = self::UnaCriptoDB($ID);
		if($cripto!=null){
			$response->getBody()->write(json_encode($cripto));
		}else{
			$stdclass->exito = false;
			$stdclass->mensaje = "Error";
			$stdclass->dato = null;
			$stdclass->exito = 424;
			$response->getBody()->write(json_encode($stdclass));
		}

		return $response->withHeader('Content-Type', 'application/json');	
	}
    public function TraerCriptos(Request $request, Response $response, array $args): Response {
		$stdclass =new stdClass();
		$cripto = self::TraerTodasLasCripto();
		if($cripto!=null){
			$response->getBody()->write(json_encode($cripto));
		}else{
			$stdclass->exito = false;
			$stdclass->mensaje = "Error";
			$stdclass->dato = null;
			$stdclass->exito = 424;
			$response->getBody()->write(json_encode($stdclass));
		}

		return $response->withHeader('Content-Type', 'application/json');	
	}
    public function TraerCriptosNacionalidad(Request $request, Response $response, array $args): Response {
        $nacionalidad = $args['nacionalidad'];
		$stdclass =new stdClass();
		$cripto = self::TraerCriptosNacBD($nacionalidad);
		if($cripto!=null){
			$response->getBody()->write(json_encode($cripto));
		}else{
			$stdclass->exito = false;
			$stdclass->mensaje = "Error";
			$stdclass->dato = null;
			$stdclass->exito = 424;
			$response->getBody()->write(json_encode($stdclass));
		}

		return $response->withHeader('Content-Type', 'application/json');	
	}
    public static function TraerCriptosNacBD($nacionalidad){
		$objetoAccesoDato = DB_PDO::InstanciarObjetoPDO("localhost","root","","cripto_bd"); 
		$consulta =$objetoAccesoDato->RetornarConsulta('SELECT * FROM criptomoneda WHERE nacionalidad = :nacionalidad');
        $consulta->bindValue(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
		$consulta->execute();			
		return $consulta->fetchAll(PDO::FETCH_CLASS, "Criptomoneda");		
	}
    public static function TraerTodasLasCripto(){
		$objetoAccesoDato = DB_PDO::InstanciarObjetoPDO("localhost","root","","cripto_bd"); 
		$consulta =$objetoAccesoDato->RetornarConsulta('SELECT * FROM criptomoneda');
		$consulta->execute();			
		return $consulta->fetchAll(PDO::FETCH_CLASS, "Criptomoneda");		
	}
    public static function UnaCriptoDB($id){
		$objetoAccesoDato = DB_PDO::InstanciarObjetoPDO("localhost","root","","cripto_bd"); 
		$consulta =$objetoAccesoDato->RetornarConsulta('SELECT * FROM criptomoneda WHERE id = :id');
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
		$consulta->execute();			
		return $consulta->fetchAll(PDO::FETCH_CLASS, "Criptomoneda");		
	}
    public function EliminarCripto(Request $request, Response $response, array $args){
		$criptoid = json_decode($request->getBody())->id_cripto;
		$datos = new stdClass();
		if($criptoid != null){
				self::Eliminar($criptoid);
				$datos->exito = true;
				$datos->mensaje = "Cripto Eliminado";
				$datos->status = 200;
			
			$response->getBody()->write(json_encode($datos));
			return $response->withHeader('Content-Type', 'application/json');
		}else{
			$datos->exito = false;
			$datos->mensaje = "No se especifica que id desea eliminar";
			$datos->status = 418;
			$response->getBody()->write(json_encode($datos));
			return $response->withHeader('Content-Type', 'application/json');
		}
		
		
	}

	public static function Eliminar($idEliminar)
	{
		$objetoAccesoDato = DB_PDO::InstanciarObjetoPDO("localhost","root","","cripto_bd"); 
		$consulta = $objetoAccesoDato->RetornarConsulta('DELETE FROM criptomoneda WHERE id = :id');
		$consulta->bindValue(':id', $idEliminar, PDO::PARAM_INT);
			
		return $consulta->execute();				
	}
}