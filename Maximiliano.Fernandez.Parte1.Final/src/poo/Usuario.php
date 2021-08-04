<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \App\Models\Usuario as UsuarioORM;
require_once "DB_PDO.php";
require_once "Autentificadora.php";

    class Usuario{

        public $correo;
        public $clave;
        public $id;
        public $tipo;

        public function VerificarUsuario(Request $request, Response $response, array $args)
        {
			$stdclass = new stdClass();
			$json = isset($request->getParsedBody()['user']) ? json_decode($request->getParsedBody()['user']) : null;

			if($json != null){
				$token = Autentificadora::CrearJWT($json);
				$stdclass->ok = true;
				$stdclass->perfil = $json->tipo;
                $stdclass->token = json_encode($token);
				$response->getBody()->write(json_encode($stdclass));
			}else{
				$stdclass->ok = false;
				$stdclass->perfil = null;
                $stdclass->token = null;
				$response->getBody()->write(json_encode($stdclass));
			}
			return $response->withHeader('Content-Type', 'application/json');
        }















        
        // public function Login(Request $request, Response $response, array $args)
        // {
		// 	$stdclass = new stdClass();
		// 	$json = isset($request->getParsedBody()['user']) ? json_decode($request->getParsedBody()['user']) : null;
        //     $datosUsuarios = Usuario::TraerDatosJWT($json)[0];
        //     $datos = new stdClass();
        //     //todos los datos menos la clave
        //     $datos->correo = $datosUsuarios->correo;
        //     $datos->nombre = $datosUsuarios->nombre;
        //     $datos->apellido = $datosUsuarios->apellido;
        //     $datos->perfil = $datosUsuarios->perfil;
        //     $datos->foto = $datosUsuarios->foto;

		// 	if($json != null){
		// 		$token = Autentificadora::CrearJWT($datos);
		// 		$stdclass->exito = true;
		// 		$stdclass->jwt = $token;
		// 		$stdclass->status = 200;
		// 		$response->getBody()->write(json_encode($stdclass));
		// 	}else{
		// 		$stdclass->exito = false;
		// 		$stdclass->jwt = null;
		// 		$stdclass->status = 403;
		// 		$response->getBody()->write(json_encode($stdclass));
		// 	}
		// 	return $response->withHeader('Content-Type', 'application/json');
        // }

		// public function VerificarToken(Request $request, Response $response, array $args) : object{   
		// 	$token =  isset($request->getHeader("token")[0]) ? $request->getHeader("token")[0] : null;
        //     $datos = new stdClass();
        //     $payload = NULL;
        //     $datos->mensaje = null;
        //     if($token != null){
        //         if(Autentificadora::VerificarJWT($token)->verificado == true){
        //             $datos->mensaje = "JWT correcto";
        //             $datos->status = 200;
        //             $response->getBody()->write(json_encode($datos));
        //             return $response->withHeader('Content-Type', 'application/json');
        //         }else{
        //             $datos->mensaje = "JWT invalido";
        //             $datos->status = 403;
        //             $response->getBody()->write(json_encode($datos));
        //             return $response->withHeader('Content-Type', 'application/json');
        //         }      
        //     }else{
        //         $datos->mensaje = "JWT No seteado";
        //         $datos->status = 403;
        //         $response->getBody()->write(json_encode($datos));
        //         return $response->withHeader('Content-Type', 'application/json');
        //     }
        
        // }
        // public function AgregarUsuario(Request $request, Response $response, array $args){
        //     $objJSON = isset($request->getParsedBody()['user']) ? json_decode($request->getParsedBody()['user']) : null;
            
        //     $file = $request->getUploadedFiles();//$_FILES
        //     $stdclass = new stdClass();
            
        //     $destiny = __DIR__ . "/../fotos/";

        //     $nameBefore = $file['foto']->getClientFilename();
        //     $extension = explode(".", $nameBefore);
        //     $extension = array_reverse($extension); 

        //     $finalyFile = $objJSON->correo."_1.". $extension[0];
        //     $idInsert = self::Insertar($objJSON,$finalyFile);
        //     if($idInsert != null){
        //         $file['foto']->moveTo($destiny . $finalyFile);
        //         $stdclass->exito = true;
        //         $stdclass->mensaje = "Usuario Agregado";
        //         $stdclass->exito = 200;
        //         $response->getBody()->write(json_encode($stdclass));
        //     }else{
        //         $stdclass->exito = false;
        //         $stdclass->mensaje = "Error Agregado";
        //         $stdclass->exito = 418;
        //         $response->getBody()->write(json_encode($stdclass));
        //     }
                
        //     return $response->withHeader('Content-Type', 'application/json');
        // }

        // public static function Insertar($obj ,$file){
        //     $objetoAccesoDato = DB_PDO::InstanciarObjetoPDO("localhost","root","","concesionaria_bd"); 
        //     $consulta = $objetoAccesoDato->RetornarConsulta('INSERT INTO usuarios(correo,clave,nombre,apellido,perfil,foto) VALUES (:correo,:clave,:nombre,:apellido,:perfil,:foto)');
        //     $consulta->bindValue(':correo',$obj->correo, PDO::PARAM_STR);
        //     $consulta->bindValue(':clave', $obj->clave, PDO::PARAM_STR);
        //     $consulta->bindValue(':nombre',$obj->nombre, PDO::PARAM_STR);
        //     $consulta->bindValue(':apellido', $obj->apellido, PDO::PARAM_STR);
        //     $consulta->bindValue(':perfil', $obj->perfil, PDO::PARAM_STR);
        //     $consulta->bindValue(':foto', $file, PDO::PARAM_STR);
        //     $consulta->execute();		
        //     return $objetoAccesoDato->RetornarUltimoIdInsertado();
        // }

        // public function TraerUsuarios(Request $request, Response $response, array $args): Response {
        //     $stdclass = new stdClass();
        //     $AllUser = self::TraerTodosDB();
        //     if($AllUser!=null){
        //         $stdclass->exito = true;
        //         $stdclass->mensaje = "Ejecutado Correctamente";
        //         $stdclass->dato = json_encode($AllUser);
        //         $stdclass->status = 200;
        //         $response->getBody()->write(json_encode($stdclass));
        //     }else{
        //         $stdclass->exito = false;
        //         $stdclass->mensaje = "Error";
        //         $stdclass->dato = null;
        //         $stdclass->status = 424;
        //         $response->getBody()->write(json_encode($stdclass));
        //     }
        //     return $response->withHeader('Content-Type', 'application/json');	
        // }

        // public function TraerUsuariosORM(Request $request, Response $response, array $args) : Response{
        //     $stdclass = new stdClass();
        //     $AllUser = usuarioORM::all();
        //     if($AllUser!=null){
        //         $stdclass->exito = true;
        //         $stdclass->mensaje = "Ejecutado Correctamente orm";
        //         $stdclass->dato = $AllUser->toJson();
        //         $stdclass->status = 200;
        //         $response->getBody()->write(json_encode($stdclass));
        //     }else{
        //         $stdclass->exito = false;
        //         $stdclass->mensaje = "Error";
        //         $stdclass->dato = null;
        //         $stdclass->status = 424;
        //         $response->getBody()->write(json_encode($stdclass));
        //     }
        //     return $response->withHeader('Content-Type', 'application/json');
        // }
        
      
      
    //   public function AgregarUsuarioORM(Request $request, Response $response, array $args) : Response{
    //     $objJSON = isset($request->getParsedBody()['user']) ? json_decode($request->getParsedBody()['user']) : null;
    //     $usuario = new usuarioORM();
  
    //     $usuario->correo = $objJSON->correo;
    //     $usuario->clave = $objJSON->clave;
    //     $usuario->nombre = $objJSON->nombre;
    //     $usuario->apellido = $objJSON->apellido;
    //     $usuario->perfil = $objJSON->perfil;
         
        
    //     $file = $request->getUploadedFiles();//$_FILES
    //     $stdclass = new stdClass();
        
    //     $destiny = __DIR__ . "/../fotos/";

    //     $nameBefore = $file['foto']->getClientFilename();
    //     $extension = explode(".", $nameBefore);
    //     $extension = array_reverse($extension); 

    //     $finalyFile = $objJSON->correo."_1.". $extension[0];
    //     if($usuario->save()){
    //         $file['foto']->moveTo($destiny . $finalyFile);
    //         $stdclass->exito = true;
    //         $stdclass->mensaje = "Usuario Agregado";
    //         $stdclass->exito = 200;
    //         $response->getBody()->write(json_encode($stdclass));
    //     }else{
    //         $stdclass->exito = false;
    //         $stdclass->mensaje = "Error Agregado";
    //         $stdclass->exito = 418;
    //         $response->getBody()->write(json_encode($stdclass));
    //     }
            
    //     return $response->withHeader('Content-Type', 'application/json');
    //     }


        // public static function TraerTodosDB(){
        //     $objetoAccesoDato = DB_PDO::InstanciarObjetoPDO("localhost","root","","concesionaria_bd"); 
        //     $consulta = $objetoAccesoDato->RetornarConsulta('SELECT * FROM usuarios');
        //     $consulta->execute();			
        //     return $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");		
        // }
        // public static function TraerDatosJWT($obj){
        //     $objetoAccesoDato = DB_PDO::InstanciarObjetoPDO("localhost","root","","concesionaria_bd"); 
        //     $consulta = $objetoAccesoDato->RetornarConsulta('SELECT usuarios.correo,usuarios.nombre,usuarios.apellido,usuarios.perfil,usuarios.foto FROM usuarios WHERE correo = :correo AND clave = :clave');
        //     $consulta->bindValue(':correo',$obj->correo, PDO::PARAM_STR);
        //     $consulta->bindValue(':clave', $obj->clave, PDO::PARAM_STR);
        //     $consulta->execute();
        //     return $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");		
        // }







        


        
    }

?>