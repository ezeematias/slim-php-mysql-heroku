<?php

class TokenApi 
{ 
  public function ObtenerToken($request, $response, $args)
  {

    $token = AutentificadorJWT::CrearToken("");

    if ($token == true) {
    $payload = json_encode(array("mensaje" => "Authorization ok", "Token: " => $token, "status" => 200));
    } else {
    $payload = json_encode(array("mensaje" => "Error al crear el token", "status" => 400));
    }
        
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }  
}