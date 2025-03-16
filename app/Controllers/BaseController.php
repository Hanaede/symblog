<?php
    namespace App\Controllers;
    use Laminas\Diactoros\Response\HtmlResponse;
    
    class BaseController {

        protected $templateEngine;

        // Constructor de la clase BaseController
        public function __construct() {
            // Configurar el cargador de plantillas de Twig
            $loader = new \Twig\Loader\FilesystemLoader("../views");
            
            // Inicializar el motor de plantillas Twig
            $this->templateEngine = new \Twig\Environment($loader, [
                "debug" => true,
                "cache" => false
            ]);
        }        

        // Método para renderizar una plantilla HTML
        public function renderHTML($fileName, $data = []){
            // Renderizar la plantilla y devolver una respuesta HTML
            return new HTMLResponse($this->templateEngine->render($fileName, $data));
        }
    }