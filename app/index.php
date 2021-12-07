<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Illuminate\Support\Facades\Route;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './middlewares/VerificacionMiddleware.php';

require_once './api/UsuarioApi.php';
require_once './api/EmpleadoApi.php';
require_once './api/ProductoApi.php';
require_once './api/MesaApi.php';
require_once './api/PedidoApi.php';
require_once './api/ComandaApi.php';
require_once './api/FileManagerApi.php';


// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();
// Set base path
$app->setBasePath('/app');

/*
ME QUEDO CON ESTE
php -S localhost:80 -t app
composer update
composer require firebase/php-jwt
composer require fpdf/fpdf

 */
// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Realiza el login y genera un token
$app->post('/login', \UsuarioApi::class . ':LoguearUsuario');
// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->post('/cargarUno', \UsuarioApi::class . ':CargarUno');                 
  $group->delete('[/{id}]', \UsuarioApi::class . ':BorrarUno'); //Dar de baja
  $group->put('/{id}', \UsuarioApi::class . ':ModificarUno');
  $group->get('/traerTodos', \UsuarioApi::class . ':TraerTodos');
  $group->get('/{usuario}', \UsuarioApi::class . ':TraerUno');
  $group->post('/{id}', \UsuarioApi::class . ':ActivarUno'); //Reactivar
})->add(\VerificacionMiddleware::class . ':VerificarAdmin')->add(\VerificacionMiddleware::class . ':ValidarToken');

/**
 * Terminado, añadir la validacion de token?
 */
$app->group('/comandas', function (RouteCollectorProxy $group) {
  $group->post('/readcsv', \FileManagerApi::class . ':ReadComandaCSV');

  $group->post('/cargarUno', \ComandaApi::class . ':CargarUno')->add(\VerificacionMiddleware::class . ':VerificarMozo'); //con cliente esperando pedido
  $group->post('/llevar_pedido', \ComandaApi::class . ':LLevarComida')->add(\VerificacionMiddleware::class . ':VerificarMozo'); //con cliente comiendo  
  $group->post('/cobrar', \ComandaApi::class . ':CobrarComanda')->add(\VerificacionMiddleware::class . ':VerificarMozo'); //con cliente pagando                                 //con cliente comiendo
  $group->post('/cerrar', \ComandaApi::class . ':CerrarComanda')->add(\VerificacionMiddleware::class . ':VerificarSocio');
  $group->delete('/{codigo}', \ComandaApi::class . ':BorrarUno')->add(\VerificacionMiddleware::class . ':VerificarSocio'); //deshabilitar
  $group->post('/modificar/{codigo}', \ComandaApi::class . ':ModificarUno')->add(\VerificacionMiddleware::class . ':VerificarMozo');
  //-- Consultas
  $group->post('/completarEncuesta', \ComandaApi::class . ':CompletarEncuesta');
  $group->post('/comentarios', \ComandaApi::class . ':VerComentarios')->add(\VerificacionMiddleware::class . ':VerificarSocio');
  $group->post('/mesaMasUsada', \ComandaApi::class . ':VerMesaMasUsada')->add(\VerificacionMiddleware::class . ':VerificarSocio');

  $group->get('/ver/{codigo}/{npedido}', \ComandaApi::class . ':VerMisPedidos');
  $group->get('/traerUno/{codigo}', \ComandaApi::class . ':TraerUno');
  $group->get('/traerComandas', \ComandaApi::class . ':TraerTodos');
  $group->get('/c/pdf', \FileManagerApi::class . ':ComandaPDF');
  $group->get('/c/csv', \FileManagerApi::class . ':ComandaCSV');

});   

$app->group('/empleados', function (RouteCollectorProxy $group) {
  $group->get('/metricas', \EmpleadoApi::class . ':MetricasEmpleado')->add(\VerificacionMiddleware::class . ':VerificarAdmin'); 
  $group->post('/cargarUno', \EmpleadoApi::class . ':CargarUno')->add(\VerificacionMiddleware::class . ':VerificarAdmin');         
  $group->delete('/{id}', \EmpleadoApi::class . ':BorrarUno')->add(\VerificacionMiddleware::class . ':VerificarAdmin');
  $group->put('/{id}', \EmpleadoApi::class . ':ModificarUno')->add(\VerificacionMiddleware::class . ':VerificarAdmin');
  $group->get('/{id}', \EmpleadoApi::class . ':TraerUno');
  $group->get('/traerTodos', \EmpleadoApi::class . ':TraerTodos');
  $group->post('/tomar_pedido', \EmpleadoApi::class . ':TomarUnPedido')->add(\VerificacionMiddleware::class . ':VerificarMozo'); //postman EMPLEADO TOMA PEDIDO
  $group->post('/entregar_pedido', \EmpleadoApi::class . ':EntregarUnPedido');  //postman EMPLEADO PREPARA PEDIDO
   $group->get('/e/pdf', \FileManagerApi::class . ':EmpleadoPDF');
   $group->get('/e/csv', \FileManagerApi::class . ':EmpleadoCSV');
});

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->post('/cargarUno', \ProductoApi::class . ':CargarUno');          // ✓
  $group->delete('/{id}', \ProductoApi::class . ':BorrarUno');      // ✓
  $group->put('/{id}', \ProductoApi::class . ':ModificarUno');      // ✓
  $group->get('/{id}', \ProductoApi::class . ':TraerUno');          // ✓
  $group->get('/traerTodos', \ProductoApi::class . ':TraerTodos');          // ✓
  //Agregar generar pdf
})->add(\VerificacionMiddleware::class . ':VerificarMozo');


$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('/metricas', \MesaApi::class . ':MetricasMesas')->add(\VerificacionMiddleware::class . ':VerificarAdmin'); 
  $group->post('/cargarUno', \MesaApi::class . ':CargarUno')->add(\VerificacionMiddleware::class . ':VerificarMozo');                // ✓
  $group->delete('/{codigo}', \MesaApi::class . ':BorrarUno')->add(\VerificacionMiddleware::class . ':VerificarSocio');       // ✓
  $group->put('/{codigo}', \MesaApi::class . ':ModificarUno')->add(\VerificacionMiddleware::class . ':VerificarMozo');        // ✓
  $group->get('/{codigo}', \MesaApi::class . ':TraerUno')->add(\VerificacionMiddleware::class . ':VerificarMozo');            // ✓
  $group->get('/traerTodos', \MesaApi::class . ':TraerTodos');
  $group->get('/m/pdf', \FileManagerApi::class . ':MesaPDF');
  $group->get('/m/csv', \FileManagerApi::class . ':MesaCSV');                                                                        // ✓
})->add(\VerificacionMiddleware::class . ':ValidarToken'); //->add(\VerificacionMiddleware::class . ':VerificarMozo');        // ✓

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('/metricas', \PedidoApi::class . ':MetricasEmpleado')->add(\VerificacionMiddleware::class . ':VerificarAdmin'); 
  $group->post('/cargar', \PedidoApi::class . ':CargarUno')->add(\VerificacionMiddleware::class . ':VerificarMozo');               // ✓
  $group->delete('/{codigo}', \PedidoApi::class . ':BorrarUno')->add(\VerificacionMiddleware::class . ':VerificarSocio');    
  $group->put('/{codigo}', \PedidoApi::class . ':ModificarUno')->add(\VerificacionMiddleware::class . ':VerificarMozo');    
  $group->get('/{codigo}', \PedidoApi::class . ':TraerUno')->add(\VerificacionMiddleware::class . ':VerificarMozo');           
  $group->get('/traerTodos', \PedidoApi::class . ':TraerTodos');
  $group->get('/p/pdf', \FileManagerApi::class . ':PedidoPDF');
  $group->get('/p/csv', \FileManagerApi::class . ':PedidoCSV');                                                                       
});//->add(\VerificacionMiddleware::class . ':VerificarSocio');

$app->get('/test', function (Request $request, Response $response) {    
  $response->getBody()->write("La Comanda - Ezequiel Unía");
  return $response;
});

$app->run();
