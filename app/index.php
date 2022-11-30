<?php
// Error Handling 
// Commit
error_reporting(-1);
ini_set('display_errors', 1);      

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

include_once './api/UsuarioAPI.php';
include_once './api/ProductoAPI.php';
include_once './api/MesaAPI.php';
include_once './api/PedidoAPI.php';
include_once './api/PedidoProductoAPI.php';
include_once './api/EncuestaAPI.php';
include_once './api/SectorAPI.php';
include_once './api/TipoUsuarioAPI.php';
include_once './api/ReportesAPI.php';


include_once './db/AccesoDatos.php';
include_once './middlewares/UsuarioMW.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__); 

$dotenv->load();

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$app->get('[/]', function (Request $request, Response $response) {    
  $response->getBody()->write("Comanda-PHP || Final");
  return $response;

});

$app->post('/empleados/login[/]', \UsuarioAPI::class . ':Login');  

//Sector
$app->group('/sector', function (RouteCollectorProxy $group) 
{
  //ABM
  $group->post('/alta[/]', \SectorAPI::class . ':Alta');
  $group->post('/modificacion[/]', \SectorAPI::class . ':Modificacion');
  $group->delete('/baja/{id}[/]', \SectorAPI::class . ':Baja');
  $group->get('/lista[/]', \SectorAPI::class . ':Listar');  
});

//Tipo de usuario
$app->group('/tipousuario', function (RouteCollectorProxy $group) 
{
  //ABM
  $group->post('/alta[/]', \TipoUsuarioAPI::class . ':Alta');
  $group->post('/modificacion[/]', \TipoUsuarioAPI::class . ':Modificacion');
  $group->delete('/baja/{id}[/]', \TipoUsuarioAPI::class . ':Baja');
  $group->get('/lista[/]', \TipoUsuarioAPI::class . ':Listar'); 
}); 

//Mesa
$app->group('/mesa', function (RouteCollectorProxy $group) 
{
  //ABM
  $group->post('/alta[/]', \MesaAPI::class . ':Alta'); 
  $group->delete('/baja/{id}[/]', \MesaAPI::class . ':Baja');
  $group->post('/modificacion[/]', \MesaAPI::class . ':Modificacion'); 
  $group->get('/lista[/]', \MesaAPI::class . ':Listar'); 
  //Import/Export
  $group->get('/export[/]', \MesaAPI::class . ':ExportarTabla');  
  $group->post('/import[/]', \MesaAPI::class . ':ImportarTabla');  
});

//Usuarios
$app->group('/empleados', function (RouteCollectorProxy $group) 
{
  //ABM
  $group->post('/alta[/]', \UsuarioAPI::class . ':Alta');
  $group->delete('/baja/{id}[/]', \UsuarioAPI::class . ':Baja');
  $group->post('/modificacion[/]', \UsuarioAPI::class . ':Modificacion'); 
  $group->get('/lista[/]', \UsuarioAPI::class . ':Listar');  
});

//Productos
$app->group('/productos', function (RouteCollectorProxy $group) 
{
  $group->post('/alta[/]', \ProductoAPI::class . ':Alta'); 
  $group->delete('/baja/{id}[/]', \ProductoAPI::class . ':Baja');
  $group->post('/modificacion[/]', \ProductoAPI::class . ':Modificacion');  
  $group->get('/lista[/]', \ProductoAPI::class . ':Listar');  
});

//Reportes
$app->group('/reportes', function (RouteCollectorProxy $group) 
{
  $group->get('/demorapedidoscerrados[/]', \ReportesAPI::class . ':DemoraPedidosCerrados');  
  $group->get('/estadomesas[/]', \ReportesAPI::class . ':EstadoMesas');  
  $group->get('/mejorescomentarios[/]', \ReportesAPI::class . ':MejoresComentarios');  
  $group->get('/mesamasusada[/]', \ReportesAPI::class . ':MesaMasUsada'); 
})
  ->add(\UsuarioMW::class. ':ValidarSocio')
  ->add(\UsuarioMW::class. ':ValidarToken');
$app->post('/reportes/demorapedidomesa[/]', \ReportesAPI::class . ':DemoraPedidoMesa'); 

//Pedido
$app->group('/pedido', function (RouteCollectorProxy $group) 
{
  //ABM
  $group->post('/alta[/]', \PedidoAPI::class . ':Alta');
  $group->delete('/baja/{id}[/]', \PedidoAPI::class . ':Baja');
  $group->post('/modificacion[/]', \PedidoAPI::class . ':Modificacion');
  //Subir Foto
  $group->post('/subirfoto[/]', \PedidoAPI::class . ':SubirFoto');
  //Manejo del pedido
  $group->get('/paraservir[/]', \ReportesAPI::class . ':PedidoProductoListoParaServir'); 
  $group->post('/comiendo[/]', \PedidoAPI::class . ':PasarAComiendo'); 
  $group->post('/pagando[/]', \PedidoAPI::class . ':PasarAPagando'); 
})
  ->add(\UsuarioMW::class. ':ValidarMozo')
  ->add(\UsuarioMW::class. ':ValidarToken');

$app->get('/pedido/lista[/]', \PedidoAPI::class . ':Listar');

//Cerrar pedido
$app->post('/pedido/cerrar[/]', \PedidoAPI::class . ':CerrarPedido') 
  ->add(\UsuarioMW::class. ':ValidarSocio')
  ->add(\UsuarioMW::class. ':ValidarToken');

//Listado de pedidos activos
$app->get('/pedido/listaBarra[/]', \PedidoProductoAPI::class . ':ListarPedidosBarra')
  ->add(\UsuarioMW::class. ':ValidarBartender')
  ->add(\UsuarioMW::class. ':ValidarToken');  
$app->get('/pedido/listaChoperas[/]', \PedidoProductoAPI::class . ':ListarPedidosChoperas')
  ->add(\UsuarioMW::class. ':ValidarCervecero')
  ->add(\UsuarioMW::class. ':ValidarToken');;  
$app->get('/pedido/listaCocina[/]', \PedidoProductoAPI::class . ':ListarPedidosCocina')
  ->add(\UsuarioMW::class. ':ValidarCocinero')
  ->add(\UsuarioMW::class. ':ValidarToken');  
$app->get('/pedido/listaCandybar[/]', \PedidoProductoAPI::class . ':ListarPedidosCandybar') 
  ->add(\UsuarioMW::class. ':ValidarRepostero')
  ->add(\UsuarioMW::class. ':ValidarToken');

//PedidoProducto
$app->group('/pedidoproducto', function (RouteCollectorProxy $group) 
{
  //ABM
  $group->post('/alta[/]', \PedidoProductoAPI::class . ':Alta'); 
  $group->delete('/baja/{id}[/]', \PedidoProductoAPI::class . ':Baja'); 
  $group->post('/modificacion[/]', \PedidoProductoAPI::class . ':Modificacion'); 
})
  ->add(\UsuarioMW::class. ':ValidarMozo')
  ->add(\UsuarioMW::class. ':ValidarToken');

//Manejo estados Pedido Producto
$app->post('/pedido/enpreparacion[/]', \PedidoProductoAPI::class . ':PedidoEnPreparacion');
$app->post('/pedido/listo[/]', \PedidoProductoAPI::class . ':PedidoListo'); 

$app->post('/encuesta/nuevaEncuesta[/]', \EncuestaAPI::class . ':Alta'); 
$app->post('/pdf/pdf[/]', \PedidoAPI::class . ':HacerPdf');

$app->run();
