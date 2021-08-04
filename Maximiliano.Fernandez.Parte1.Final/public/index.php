<?php

use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollector;
use \Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//necesario para las vistas
require __DIR__ . '/../vendor/autoload.php';
// require __DIR__ . '/../src/poo/Auto.php';
require __DIR__ . '/../src/poo/Criptomoneda.php';
require __DIR__ . '/../src/poo/VentaCripto.php';
require __DIR__ . '/../src/poo/Usuario.php';
require __DIR__ . '/../src/poo/MW.php';
//referenciar la clase del modelo


// y usar un alias para el namespace de la entidad Eloquent ORM
// use \App\Models\Usuario as UsuarioORM;

$app = AppFactory::create();



//************************************************************************************************************// 
$app->post("/" ,Usuario::class . ':VerificarUsuario')->add(MW::class . ':VerificarDatosUsuario');
$app->post("/altaCripto" ,Criptomoneda::class . ':AgregarCripto')->add(MW::class . ':SoloAdmin');
$app->get("/" ,Criptomoneda::class . ':TraerCriptos');
$app->get("/nacionalidad/{nacionalidad}" ,Criptomoneda::class . ':TraerCriptosNacionalidad');
$app->get("/id/{id}" ,Criptomoneda::class . ':TraerUnaCripto')->add(MW::class . ':Registrado');
$app->post("/ventaCripto" ,VentaCripto::class . ':AltaventaCripto')->add(MW::class . ':Registrado');
$app->get("/traerAlemanas" ,VentaCripto::class . ':TraerVentasAlemanas')->add(MW::class . ':SoloAdmin');

$app->delete("/" ,Criptomoneda::class . ':EliminarCripto');













// $app->post('/usuarios',Usuario::class . ':AgregarUsuario')->add(MW::class . ':VerificarCorreo')->add(MW::class . '::VerificarVacio')->add(MW::class . ':ValidarUsuarioSeteado');

//  $app->get("/" ,Usuario::class . ':TraerUsuarios');

//  $app->post("/" ,Auto::class . ':AgregarAuto')->add(MW::class . ':VerificarPrecioyColor');

//  $app->get("/autos" ,Auto::class . ':TraerAutos');

//  $app->post("/login" ,Usuario::class . ':Login')->add(MW::class . ':VerificarBD')->add(MW::class . '::VerificarVacio')->add(MW::class . ':ValidarUsuarioSeteado');

//  $app->get("/login" ,Usuario::class . ':VerificarToken');

//  $app->delete("/" ,Auto::class . ':EliminarAuto')->add(MW::class . ':VerificarPropietario');

//  $app->put("/" ,Auto::class . ':ModificarAuto');
 
 
 

// $app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
//     $name = $args['name'];
//     $response->getBody()->write("Hello, ". $name);
//     return $response;
// });
 $app->run();




