<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;
use Firebase\JWT\JWT; 

require_once "DB_PDO.php";
    class MW{
        // public function ValidarUsuarioSeteado(Request $request, RequestHandler $handler) : ResponseMW{
            
        //     $objJSON = isset($request->getParsedBody()['user']) ? json_decode($request->getParsedBody()['user']) : null;
        //     $response = new ResponseMW();
        //     $stdClass = new stdClass();
        //     if($objJSON != null){
        //         if(!isset($objJSON->correo) && !isset($objJSON->clave)){
        //             $stdClass->mensaje = "no existe el correo y la clave";
        //             $stdClass->status = 403;
        //             $response->getBody()->write(json_encode($stdClass));
        //             return $response->withHeader('Content-Type', 'application/json');
        //         }
        //         if(!isset($objJSON->correo)){
        //             $stdClass->mensaje = "no existe el correo";
        //             $stdClass->status = 403;
        //             $response->getBody()->write(json_encode($stdClass));
        //             return $response->withHeader('Content-Type', 'application/json');
        //         }
        //         if(!isset($objJSON->clave)){
        //             $stdClass->mensaje = "no existe la clave";
        //             $stdClass->status = 403;
        //             $response->getBody()->write(json_encode($stdClass));
        //             return $response->withHeader('Content-Type', 'application/json');
        //         }
        //         $response = $handler->handle($request);
        //         return $response;
        //     }else{
        //         $stdClass->mensaje = "no existe el JSON";  
        //         $stdClass->status = 403;
        //         $response->getBody()->write(json_encode($stdClass));
        //         return $response->withHeader('Content-Type', 'application/json');
        //     }
            
        // }

        // public static function VerificarVacio(Request $request, RequestHandler $handler) : ResponseMW{
        //     $stdClass = new stdClass();
        //     $objJSON = isset($request->getParsedBody()['user']) ? json_decode($request->getParsedBody()['user']) : null;
        //     $response = new ResponseMW();
        //     if($objJSON != null){
        //         if($objJSON->correo === "" && $objJSON->clave === ""){
        //             $stdClass->mensaje = "Correo y Clave Vacios";
        //             $stdClass->status = 409;
        //             $response->getBody()->write(json_encode($stdClass));
        //             return $response->withHeader('Content-Type', 'application/json');
        //         }
        //         if($objJSON->correo === ""){
        //             $stdClass->mensaje = "Correo Vacio";
        //             $stdClass->status = 409;
        //             $response->getBody()->write(json_encode($stdClass));
        //             return $response->withHeader('Content-Type', 'application/json');
        //         }
        //         if($objJSON->clave === ""){
        //             $stdClass->mensaje = "Clave Vacia";
        //             $stdClass->status = 409;
        //             $response->getBody()->write(json_encode($stdClass));
        //             return $response->withHeader('Content-Type', 'application/json');
        //         }
        //         $response = $handler->handle($request);
        //         return $response;   
                
        //     }else{
        //         $stdClass->mensaje = "no existe el objeto json";  
        //         $stdClass->status = 403;
        //         $response->getBody()->write(json_encode($stdClass));
        //         return $response->withHeader('Content-Type', 'application/json');
        //     }

        // }

        // public function VerificarBD(Request $request,RequestHandler $handler) : ResponseMW{
        //     $stdClass = new stdClass();
        //     $newResponse = new ResponseMW();
        //     $datosJSON = json_decode($request->getParsedBody()['user']);
        //     $User = self::TraerCorreoyClave($datosJSON);
        //     if($User != null){
        //         $response = $handler->handle($request);
        //         return $response;
        //     }else{
        //         $stdClass->mensaje = "No existe la clave y el correo en la Base de Datos";
        //         $stdClass->status = 403;
        //         $newResponse->getBody()->write(json_encode($stdClass));
        //         return $newResponse->withHeader('Content-Type', 'application/json');
        //     }

        // }
        public function VerificarDatosUsuario(Request $request,RequestHandler $handler) : ResponseMW{
            $stdClass = new stdClass();
            $newResponse = new ResponseMW();
            $datosJSON = json_decode($request->getParsedBody()['user']);
            $User = self::TraerDatos($datosJSON);
            if($User != null){
                $response = $handler->handle($request);
                return $response;
            }else{
                $stdClass->mensaje = "NO existe el usuario en la base de datos";
                $stdClass->status = 403;
                $newResponse->getBody()->write(json_encode($stdClass));
                return $newResponse->withHeader('Content-Type', 'application/json');
            }

        }
        public function SoloAdmin(Request $request,RequestHandler $handler) : ResponseMW{
            $token =  isset($request->getHeader("token")[0]) ? $request->getHeader("token")[0] : null;
            $stdClass = new stdClass();
            $newResponse = new ResponseMW();
            // $datosJSON = json_decode($request->getParsedBody()['cripto']);
            $data = Autentificadora::ObtenerDataPayLoad($token);
            if($token != null && $data != null){
                if($data->tipo == "admin"){
                    $response = $handler->handle($request);
                    return $response;
                }else{
                    $stdClass->mensaje = "No es admin";
                    $stdClass->status = 403;
                    $newResponse->getBody()->write(json_encode($stdClass));
                    return $newResponse->withHeader('Content-Type', 'application/json');
                }
            }else{
                $stdClass->mensaje = "JWT invalido";
                $stdClass->status = 403;
                $newResponse->getBody()->write(json_encode($stdClass));
                return $newResponse->withHeader('Content-Type', 'application/json'); 
            }
            

        }
        public function Registrado(Request $request,RequestHandler $handler) : ResponseMW{
            $token =  isset($request->getHeader("token")[0]) ? $request->getHeader("token")[0] : null;
            $stdClass = new stdClass();
            $newResponse = new ResponseMW();
            $data = Autentificadora::ObtenerDataPayLoad($token);
            if($token != null && $data != null){
                if($data->tipo == "admin" || $data->tipo == "cliente"){
                    $response = $handler->handle($request);
                    return $response;
                }else{
                    $stdClass->mensaje = "No esta registrado";
                    $stdClass->status = 403;
                    $newResponse->getBody()->write(json_encode($stdClass));
                    return $newResponse->withHeader('Content-Type', 'application/json');
                }
            }
            

        }
    	// public static function TraerCorreoyClave($datosJSON) {
        //     $correo = $datosJSON->correo;
        //     $clave = $datosJSON->clave;
        //     $objetoAccesoDato = DB_PDO::InstanciarObjetoPDO("localhost","root","","concesionaria_bd"); 
        //     $consulta =$objetoAccesoDato->RetornarConsulta("SELECT usuarios.correo,usuarios.clave,usuarios.nombre,usuarios.apellido,usuarios.perfil,usuarios.foto FROM usuarios WHERE usuarios.correo=:correo AND usuarios.clave=:clave");
        //     $consulta->bindValue(':correo',$correo, PDO::PARAM_STR);
        //     $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        //     $consulta->execute();
        //     $buscado = $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");
        //     return $buscado;		
        // }
//////
        public static function TraerDatos($datosJSON) {
            $correo = $datosJSON->correo;
            $clave = $datosJSON->clave;
            $tipo = $datosJSON->tipo;
            $objetoAccesoDato = DB_PDO::InstanciarObjetoPDO("localhost","root","","cripto_bd"); 
            $consulta =$objetoAccesoDato->RetornarConsulta("SELECT usuarios.mail,usuarios.clave,usuarios.tipo FROM usuarios WHERE usuarios.mail=:mail AND usuarios.clave=:clave AND usuarios.tipo=:tipo");
            $consulta->bindValue(':mail',$correo, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
            $consulta->execute();
            $buscado = $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");
            return $buscado;		
        }

        public static function VerificarCorreo(Request $request,RequestHandler $handler) : ResponseMW
        {
            $stdClass = new stdClass();
            $newResponse = new ResponseMW();
            $datosJSON = json_decode($request->getParsedBody()['user']);
            $User = self::TraerCorreo($datosJSON);
            if($User != null){
                $stdClass->mensaje = "El correo ya existe en la base de datos";
                $stdClass->status = 409;
                $newResponse->getBody()->write(json_encode($stdClass));
                return $newResponse->withHeader('Content-Type', 'application/json');
            }else{
                $response = $handler->handle($request);
                return $response;
            }

        }
        public static function TraerCorreo($datosJSON) {
            $correo = $datosJSON->correo;
            $objetoAccesoDato = DB_PDO::InstanciarObjetoPDO("localhost","root","","concesionaria_bd"); 
            $consulta =$objetoAccesoDato->RetornarConsulta("SELECT usuarios.correo,usuarios.clave,usuarios.nombre,usuarios.apellido,usuarios.perfil,usuarios.foto FROM usuarios WHERE usuarios.correo=:correo");
            $consulta->bindValue(':correo',$correo, PDO::PARAM_STR);
            $consulta->execute();
            $buscado = $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");
            return $buscado;		
        }

        public function VerificarPrecioyColor(Request $request,RequestHandler $handler) : ResponseMW
        {
            $stdClass = new stdClass();
            $newResponse = new ResponseMW();
            $datosJSON = json_decode($request->getParsedBody()['auto']);
            if($datosJSON->color != "amarillo" && ($datosJSON->precio < 50000 || $datosJSON->precio > 600000)){
                $stdClass->mensaje = "Color y rango de precio incorrecto";
                $stdClass->status = 409;
                $newResponse->getBody()->write(json_encode($stdClass));
                return $newResponse->withHeader('Content-Type', 'application/json');
                
            }
            if($datosJSON->color != "amarillo"){
                $stdClass->mensaje = "Color incorrecto";
                $stdClass->status = 409;
                $newResponse->getBody()->write(json_encode($stdClass));
                return $newResponse->withHeader('Content-Type', 'application/json');
                
            }
            if(($datosJSON->precio < 50000 || $datosJSON->precio > 600000)){
                $stdClass->mensaje = "Rango de precio incorrecto";
                $stdClass->status = 409;
                $newResponse->getBody()->write(json_encode($stdClass));
                return $newResponse->withHeader('Content-Type', 'application/json');
                
            }
          
            $response = $handler->handle($request);
            return $response;
            

        }
        public function VerificarToken(Request $request,RequestHandler $handler) : ResponseMW{   
			$token =  isset($request->getHeader("token")[0]) ? $request->getHeader("token")[0] : null;
            $datos = new stdClass();
            $newResponse = new ResponseMW();
            $datos->mensaje = null;
            if($token != null){
                if(Autentificadora::VerificarJWT($token)->verificado == true){
                    $response = $handler->handle($request);
                    return $response;
                }else{
                    $datos->mensaje = "JWT invalido";
                    $datos->status = 403;
                    $newResponse->getBody()->write(json_encode($datos));
                    return $newResponse->withHeader('Content-Type', 'application/json');
                }      
            }else{
                $datos->mensaje = "JWT No seteado";
                $datos->status = 403;
                $newResponse->getBody()->write(json_encode($datos));
                return $newResponse->withHeader('Content-Type', 'application/json');
            }
        
        }
        
        public static function VerificarPropietario(Request $request,RequestHandler $handler) : ResponseMW{
            $datos = new stdClass();
            $token = isset($request->getHeader("token")[0]) ? $request->getHeader("token")[0] : null;
            $newResponse = new ResponseMW();
            $data = Autentificadora::ObtenerDataPayLoad($token);
            //$data = Autentificadora::ObtenerPayLoad($token)->payload->data;
            if($token != null && $data != null){
                if($data->perfil == "propietario"){
                    $datos->propietario = true;
                    $datos->mensaje = "Es propietario";
                    $datos->status = 200;
                    $newResponse->getBody()->write(json_encode($datos));
                    return $newResponse->withHeader('Content-Type', 'application/json');
                }else{
                    $datos->propietario = false;
                    $datos->mensaje = "NO es propietario";
                    $datos->status = 409;
                    $newResponse->getBody()->write(json_encode($datos));
                    return $newResponse->withHeader('Content-Type', 'application/json');
                } 
            }else{
                $datos->propietario = false;
                $datos->mensaje = "No jwt";
                $datos->status = 409;
                $newResponse->getBody()->write(json_encode($datos));
                return $newResponse->withHeader('Content-Type', 'application/json');
            }

        }
        public function VerificarEncargado(Request $request,RequestHandler $handler) : ResponseMW{
            $datos = new stdClass();
            $token = isset($request->getHeader("token")[0]) ? $request->getHeader("token")[0] : null;
            $newResponse = new ResponseMW();
            $data = Autentificadora::ObtenerPayLoad($token)->payload->data;
            if($token != null && $data != null){
                if($data->perfil == "encargado"){
                    $datos->encargado = true;
                    $datos->mensaje = "Es encargado";
                    $datos->status = 200;
                    $newResponse->getBody()->write(json_encode($datos));
                    return $newResponse->withHeader('Content-Type', 'application/json');
                }else{
                    $datos->encargado = false;
                    $datos->mensaje = "NO es encargado";
                    $datos->status = 409;
                    $newResponse->getBody()->write(json_encode($datos));
                    return $newResponse->withHeader('Content-Type', 'application/json');
                } 
            }else{
                $datos->propietario = false;
                $datos->mensaje = "No jwt";
                $datos->status = 409;
                $newResponse->getBody()->write(json_encode($datos));
                return $newResponse->withHeader('Content-Type', 'application/json');
            }

        }

    }

?>