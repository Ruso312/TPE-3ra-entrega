<?php
require_once 'libs/Router.php';
require_once 'app/controller/rutina.api.controller.php';

$router = new Router();

$router->addRoute('rutina','GET','RutinasController','obtenerRutinas');
$router->addRoute('rutina/:ID','GET','RutinasController','obtenerRutinaCliente');
$router->addRoute('rutina','POST','RutinasController','agregarRutina');
$router->addRoute('rutina/:ID','PUT','RutinasController','actualizarRutina');

$router->route($_GET['resource'], $_SERVER['REQUEST_METHOD']);