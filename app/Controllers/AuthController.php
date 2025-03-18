<?php
     namespace App\Controllers;
     include "../lib/lib.php";
     use App\Controllers\BaseController;
     use Laminas\Diactoros\Response\RedirectResponse;
     use App\Models\Blog;
     use App\Models\Usuario;

    class AuthController extends BaseController{

        // Muestra la vista de inicio de sesión
        public function GetLoginAction() {
            // Obtiene los últimos 5 comentarios y las etiquetas
            $data["allComments"] = array_reverse(array_slice(getAllComments(Blog::all()), -5));
            $data["tags"] = printTags();
            
            // Renderiza la vista de inicio de sesión con los datos obtenidos
            return $this->renderHTML("login_view.twig", [
                "allComments" => $data["allComments"],
                "tags" => $data["tags"],
            ]);
        }

        // Maneja el envío del formulario de inicio de sesión
        public function PostLoginAction($request) {
            // Obtiene los últimos 5 comentarios y las etiquetas
            $data["allComments"] = array_reverse(array_slice(getAllComments(Blog::all()), -5));
            $data["tags"] = printTags();
            
            // Obtiene los datos del formulario
            $postData = $request->getParsedBody();
            $response = null;
        
            // Busca al usuario por su correo electrónico
            $user = Usuario::where("email", $postData["email"])->first();
            if($user){
                // Verifica la contraseña
                if($postData["passwd"] == $user->password){
                    // Inicia la sesión y redirige al usuario a la página de administración
                    $_SESSION["user"] = $user;
                    $_SESSION["profile"] = $user->profile;
                    $response = "OK Credentials";

                    return new RedirectResponse("/admin");
                } else {
                    // Contraseña incorrecta
                    // echo "poosdata   ".$postData["passwd"];
                    // echo "user pass  ".$user->password;
                    $response = "Bad Credentials 222";
                }
            } else{
                // Usuario no encontrado
                $response = "Bad Credentials";
            }
            $data["response"] = $response;
            // Renderiza la vista de inicio de sesión con el mensaje de error
            return $this->renderHTML("login_view.twig", [
                "allComments" => $data["allComments"],
                "tags" => $data["tags"],
            ]);
        }

        // Cierra la sesión del usuario
        public function LogOutAction() {
            unset($_SESSION["profile"]);
            // Redirige al usuario a la página de inicio de sesión
            return new RedirectResponse("/login");
        }
    }