<?php
     namespace App\Controllers;

     use App\Controllers\BaseController;
     use Laminas\Diactoros\Response\RedirectResponse;
     include "../lib/lib.php";
     use App\Models\Blog;
     use App\Models\Usuario;

    class UserController extends BaseController {

        // Método para agregar un nuevo usuario
        public function AddAction($request) {
            // Obtener los datos enviados en la solicitud
            $postData = $request->getParsedBody();

            // Crear un nuevo usuario con los datos proporcionados
            Usuario::create([
                "user" => $postData["user"],
                'password' => $postData['passwd'],
                'email' => $postData['email']
            ]);
            
            // Establecer una cookie para indicar que se ha creado un nuevo usuario
            $cookie = setcookie("newUser", true, time() + 60,"/");

            // Obtener los últimos 5 comentarios y las etiquetas
            $data["allComments"] = array_reverse(array_slice(getAllComments(Blog::all()), -5));
            $data["tags"] = printTags();

            // Renderizar la vista "addUser_view.twig" con los datos obtenidos
            return $this->renderHTML("addUser_view.twig", [
                "allComments" => $data["allComments"],
                "tags" => $data["tags"],
                "cookie" => $cookie
            ]);
        }

        // Método para mostrar la vista de agregar un nuevo usuario
        public function NewAction() {
            // Obtener los últimos 5 comentarios y las etiquetas
            $data["allComments"] = array_reverse(array_slice(getAllComments(Blog::all()), -5));
            $data["tags"] = printTags();

            // Renderizar la vista "addUser_view.twig" con los datos obtenidos
            return $this->renderHTML("addUser_view.twig", [
                "allComments" => $data["allComments"],
                "tags" => $data["tags"]
            ]);
        }
    }