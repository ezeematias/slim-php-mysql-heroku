<?php

use GuzzleHttp\Psr7\Response;

require_once './JWT/AutentificadorJWT.php';
class TokenMiddleware
{
    public static function ValidarToken($request, $handler)
    {
        $response = new Response();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $esValido = false;
        try {
            AutentificadorJWT::VerificarToken($token);
            $esValido = true;
        } catch (Exception $e) {
            echo ("<br>error: " . $e->getMessage() . "</br>");
            $payload = json_encode(array(
                'error' => $e->getMessage()
            ));
            $response->getBody()->write($payload);
        }
        if ($esValido) {
            $response = $handler->handle($request);
        }        
        return $response->withHeader('Content-Type', 'application/json');
    }
}
