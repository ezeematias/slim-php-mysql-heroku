<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';
require_once './models/Logger.php';
require_once './models/AutentificadorJWT.php';

class UsuarioApi extends Usuario implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $ArrayDeParametros = $request->getParsedBody();
    $arrayUsuario = $ArrayDeParametros['usuarioC'];
        
    $usuario = $arrayUsuario['usuario'];
    $clave = $arrayUsuario['clave'];
    $perfil = $arrayUsuario['perfil'];

    // Creamos el usuario
    $usr = new Usuario();
    $usr->usuario = $usuario;
    $usr->clave = $clave;
    $usr->perfil = $perfil;
    $usr->crearUsuario();

    $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  //Funcion para loguear usuario y crear token
  public function LoguearUsuario($request, $response, $args)
  {
    $ArrayDeParametros = $request->getParsedBody();
    $arrayUsuario = $ArrayDeParametros['usuario'];
    $usuario = $arrayUsuario['usuario'];
    $clave = $arrayUsuario['clave'];

    $respuesta = Usuario::validarUsuario($usuario, $clave);

    if ($respuesta) {
      $ingreso = array("usuario" => $usuario, "clave" => $clave);
      $token = AutentificadorJWT::CrearToken($ingreso);

      if ($token == true) {
        $payload = json_encode(array("mensaje" => "Authorization ok", "Token: " => $token, "status" => 200));
      } else {
        $payload = json_encode(array("mensaje" => "Error al crear el token", "status" => 400));
      }
    } else {
      $payload = json_encode(array("mensaje" => "Error al validar el empleado", "status" => 400));
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos usuario por nombre
    $usr = $args['usuario'];
    $usuario = Usuario::obtenerUsuario($usr);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Usuario::obtenerTodos();
    $payload = json_encode(array("listaUsuario" => $lista));
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $id = $args['id'];
    $ArrayDeParametros = $request->getParsedBody();
    $arrayUsuario = $ArrayDeParametros['modificar'];

    //Crear un nuevo usuario
    $usr = new Usuario();
    $usr->usuario = $arrayUsuario['usuario'];
    $usr->clave = $arrayUsuario['clave'];
    $usr->perfil = $arrayUsuario['perfil'];
    $filasAfectadas = $usr->modificarUsuario($id);

    if ($filasAfectadas > 0) {
      $new_log = new Logger();
      $new_log->idEmpleado =  $id;
      $new_log->accion = "Usuario modificado";
      $new_log->InsertarLog();

      $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Error al modificar el usuario"));
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $usuarioId = $args['id'];

    $filasAfectadas = Usuario::darDeBajaUsuario($usuarioId);
    if ($filasAfectadas > 0) {

      $new_log = new Logger();
      $new_log->idEmpleado =  $usuarioId;
      $new_log->accion = "Usuario dado de baja";
      $new_log->InsertarLog();

      $payload = json_encode(array("<li>mensaje" => "Usuario dado de baja con exito", "status" => 200));
    } else {
      $payload = json_encode(array("<li>mensaje: " => "Error al eliminar el empleado", "status" => 400));
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function ActivarUno($request, $response, $args)
  {
    $usuarioId = $args['id'];

    $filasAfectadas = Usuario::activarUsuario($usuarioId);
    if ($filasAfectadas > 0) {

      $new_log = new Logger();
      $new_log->idEmpleado =  $usuarioId;
      $new_log->accion = "Usuario reactivado";
      $new_log->InsertarLog();

      $payload = json_encode(array("<li>mensaje" => "Usuario reactivado con exito", "status" => 200));
    } else {
      $payload = json_encode(array("<li>mensaje: " => "Error al reactivar el usuario", "status" => 400));
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
