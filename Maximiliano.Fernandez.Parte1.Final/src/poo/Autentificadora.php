<?php
use Firebase\JWT\JWT;

class Autentificadora
{
    private static $secret_key = 'ClaveSuperSecreta@';
    private static $encrypt = ['HS256'];
    private static $aud = NULL;
    
    public static function CrearJWT($data, $exp = (60*120)) : string{
        $time = time();
        self::$aud = self::Aud();

        $token = array(
        	'iat'=>$time,
            'exp' => $time + $exp,
            'aud' => self::$aud,
            'data' => $data,
            'app'=> "API REST 2021"
        );

        return JWT::encode($token, self::$secret_key);
    }
    
    public static function VerificarJWT($token) : stdClass
    {
        $datos = new stdClass();
        $datos->verificado = FALSE;
        $datos->mensaje = "";

        try 
        {
            if( ! isset($token))
            {
                $datos->mensaje = "Token vacío!!!";
            }
            else
            {          
                $decode = JWT::decode(
                    $token,
                    self::$secret_key,
                    self::$encrypt
                );

                if($decode->aud !== self::Aud())
                {
                    $datos->verificado = false;
                    $datos->mensaje = "Usuario Invalido!!!";
                }
                else
                {
                    $datos->verificado = true;
                    $datos->mensaje = "Token OK!!!";
                } 
            }          
        } 
        catch (Exception $e) 
        {
            $datos->mensaje = "Token inválido!!! - " . $e->getMessage();
        }
    
        return $datos;
    }
    
    public static function ObtenerPayLoad($token) : object
    {
        $datos = new stdClass();
        $datos->exito = false;
        $datos->payload = null;
        $datos->mensaje = "";

        try {
            $datos->payload = JWT::decode(
                                            $token,
                                            self::$secret_key,
                                            self::$encrypt
                                        );
            $datos->exito = true;

        } catch (Exception $e) { 

            $datos->mensaje = $e->getMessage();
        }

        return $datos;
    }

    public static function ObtenerDataPayLoad($token)
    {
        $data = null;
        try {
            $data = JWT::decode(
                                            $token,
                                            self::$secret_key,
                                            self::$encrypt
                                        )->data;                      
        } catch (Exception $e) { 

            $data = null;
        }

        return $data;
    }
    
    private static function Aud() : string
    {
        $aud = new stdClass();
        $aud->ip_visitante = "";

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud->ip_visitante = $_SERVER['HTTP_CLIENT_IP'];
        } 
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud->ip_visitante = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud->ip_visitante = $_SERVER['REMOTE_ADDR'];//La dirección IP desde la cual está viendo la página actual el usuario.
        }
        
        $aud->user_agent = @$_SERVER['HTTP_USER_AGENT'];
        $aud->host_name = gethostname();
        
        return json_encode($aud);//sha1($aud);
    }

}