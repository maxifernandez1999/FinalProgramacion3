<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \App\Models\Usuario as UsuarioORM;
require_once "DB_PDO.php";
require_once "Autentificadora.php";

class VentaCripto{
    public $id;
    public $fecha;
    public $cantidad;
    public $foto;
    public $nacionalidad;
    public $nombre;
    public function AltaventaCripto(Request $request, Response $response, array $args){
        $objJSON = isset($request->getParsedBody()['cripto']) ? json_decode($request->getParsedBody()['cripto']) : null;
        $token =  isset($request->getHeader("token")[0]) ? $request->getHeader("token")[0] : null;
        if($token != null){
            $cliente = Autentificadora::ObtenerDataPayLoad($token);
        }
        
        $file = $request->getUploadedFiles();//$_FILES
        $stdclass = new stdClass();
        
        $destiny = __DIR__ . "/../fotosCripto/";

        $nameBefore = $file['foto']->getClientFilename();
        $extension = explode(".", $nameBefore);
        $extension = array_reverse($extension); 

        $finalyFile = $objJSON->nombre.".".$objJSON->fecha.".". $extension[0];
        $idInsert = self::Insertar($objJSON,$finalyFile);
        if($idInsert != null){
            $file['foto']->moveTo($destiny . $finalyFile);
            
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
        $consulta = $objetoAccesoDato->RetornarConsulta('INSERT INTO ventaCripto(fecha,cantidad,foto,nombre,nacionalidad) VALUES (:fecha,:cantidad,:foto,:nombre,:nacionalidad)');
        $consulta->bindValue(':fecha',$obj->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $obj->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':foto',$file, PDO::PARAM_STR);
        $consulta->bindValue(':nombre',$obj->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':nacionalidad',$obj->nacionalidad, PDO::PARAM_STR);
        $consulta->execute();		
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }
    public function TraerVentasAlemanas(Request $request, Response $response, array $args): Response {
        $retorno = true;
		$stdclass = new stdClass();
		$criptoJSON = self::TraerTodasLasVentasAlemanas();
		if($criptoJSON!=null){
            foreach ($criptoJSON as $cripto) {
                $criptoExplode = explode("-", $cripto->fecha);

                if((intval($criptoExplode[0]) >=10 && intval($criptoExplode[0]) <=13) && $criptoExplode[1] == "6" && $cripto->nacionalidad == "alemana"){
                $response->getBody()->write(json_encode($cripto));
                }else{
                    $retorno = false;
                }
            }
            
		}else{
			$stdclass->exito = false;
			$stdclass->mensaje = "Error";
			$response->getBody()->write(json_encode($stdclass));
		}
        if($retorno == true){
            $stdclass->exito = false;
            $stdclass->mensaje = "no se encuentro ventas con la fecha o  la nacionalidad";
            $response->getBody()->write(json_encode($stdclass));
        }

		return $response->withHeader('Content-Type', 'application/json');	
	}
    public static function TraerTodasLasVentasAlemanas(){
		$objetoAccesoDato = DB_PDO::InstanciarObjetoPDO("localhost","root","","cripto_bd"); 
		$consulta =$objetoAccesoDato->RetornarConsulta('SELECT * FROM ventacripto');
		$consulta->execute();			
		return $consulta->fetchAll(PDO::FETCH_CLASS, "VentaCripto");		
	}
}