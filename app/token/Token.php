<?php

require "../vendor/autoload.php";
use Firebase\JWT\JWT;

class Token
{
    private static $clave = 'P44r$Tpp';
    private static $encriptacion = ['HS256'];

    public static function GenerarToken($idUsuario, $tipo)
    {
        $time_now = time();
        $payload = array(
            'iat' => $time_now,
            'exp' => $time_now + (60000)*24*365,
            'idUsuario' => $idUsuario,
            'tipo' => $tipo,
        );
        return JWT::encode($payload, self::$clave, 'HS256');
    }

    public static function verifyToken($token) 
    {
        if (empty($token)) 
        {
            throw new Exception("The token is empty.");
        }
        try 
        {
            $decoded = JWT::decode($token, self::$clave, self::$encriptacion);
            return $decoded;
        } 
        catch (Exception $e) 
        {
            throw $e;
        }
        /*if ($decoded->aud !== self::Aud()) {
            throw new Exception("User wrong");
        }*/
    }

    private static function Aud() 
    {
        $aud = '';

        if(!empty($_SERVER['HTTP_CLIENT_IP'])) 
        {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } 
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
        {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } 
        else 
        {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}

?>