<?php

require_once 'app/controller/api.controller.php';
require_once 'app/model/rutina.model.php';

class RutinasController extends ApiController{
    protected $model;

    function __construct() {
        parent::__construct();
        $this->model = new RutinasModel();
    }
    
    function obtenerRutinas($parametros = []){
        $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1; // Página actual
        $porPagina = isset($_GET['por_pagina']) ? $_GET['por_pagina'] : 10; // Elementos por página
        
        //Verificamos que los datos ingresados por la URL sean validos.
        if (!is_numeric($pagina) || !is_numeric($porPagina) || $pagina <= 0 || $porPagina <= 0) {
            $this->view->response("Parámetros de paginación inválidos.", 400);
            return;
        }
        
        $sql = 'SELECT * FROM rutina';
        
        $order = isset($_GET['order']) ? $_GET['order'] : null;
        $sort =  isset($_GET['sort']) ? $_GET['sort'] : 'ASC';
        
        if (isset($order)) {
            // Lista blanca de nombres de columnas permitidos
            $columnasPermitidas = ['tren','musculo','ejercicio','repeticiones','series'];
            // Verifica si el nombre de columna es permitido
            $ordenColumna = in_array($order, $columnasPermitidas) ? $order : 'id';
            $sql .= ' ORDER BY ' . $ordenColumna;
            
            if (isset($sort)) {
                $ordenSort = strtoupper($sort) == 'DESC' ? 'DESC' : 'ASC';
                $sql .= ' ' . $ordenSort;
            }
            else{
                $this->view->response("Error en la carga.", 400);
                return;
            }
        }
        //Implementamos la paginacion a la peticion sql.
        $inicio = ($pagina - 1) * $porPagina;
        $sql .= ' LIMIT ' . $inicio . ', ' . $porPagina;
        
            $rutinas = $this->model->obtenerRutinas($sql);

            if ($rutinas === false) {
                $this->view->response("Error al obtener las rutinas.", 500);
                return;
            }

            $this->view->response($rutinas, 200);
            return $rutinas;
    }

    function obtenerRutinaCliente($params = []){
        if(empty($params)){
        $rutinas = $this->model->obtenerRutinas();
        $this->view->response($rutinas, 200);
        }
        else{
            $rutina = $this->model->obtenerRutinasCliente($params[":ID"]);
            if(!empty($rutina)){
                return $this->view->response($rutina,200);
            }
            else{
                $this->view->response('No existe un usuario con ID '.$params[':ID'], 404);
            }
        }
    }

    function agregarRutina($params = []){
        $body = $this->getData();

        $tren = $body->tren;
        $musculo = $body->musculo;
        $ejercicio = $body->ejercicio;
        $repeticiones = $body->repeticiones;
        $series = $body->series;
        
        $cliente_id = $body->cliente_id; 
        //En una web completa este dato se obtendría desde el login con $_SESSION['CLIENTE_ID]'
        
        
        $id = $this->model->agregarRutina($tren,$musculo,$ejercicio,$repeticiones,$series,$cliente_id);

        $this->view->response('La rutina fue creada con el id = '. $id, 201);


    }

    function actualizarRutina($params = []) {
        $id = $params[':ID'];
        if ($this->model->obtenerRutina($id)) {
            $body = $this->getData();

            $tren = $body->tren;
            $musculo = $body->musculo;
            $ejercicio = $body->ejercicio;
            $repeticiones = $body->repeticiones;
            $series = $body->series;
            
            $this->model->actualizarRutina($tren, $musculo, $ejercicio, $repeticiones, $series, $id);
            $this->view->response('La rutina id = '. $id . ' se ha actualizado con éxito', 200);
        }
        else{
            $this->view->response('La rutina con id = '. $id . ' no existe', 404);
        }
    }
}