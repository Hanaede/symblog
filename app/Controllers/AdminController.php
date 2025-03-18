<?php
     namespace App\Controllers;
     include "../lib/lib.php";
     use Laminas\Diactoros\Response\HTMLResponse; //Respuestas y solicitudes HTTP
     use App\Models\Blog;

    class AdminController extends BaseController
    {
        // Método para mostrar la vista de administración
        public function AdminAction() {
            // Obtener los últimos 5 comentarios
            $data["allComments"] = array_reverse(array_slice(getAllComments(Blog::all()), -5));
            $data["tags"] = printTags();

            // Obtener información del usuario de la sesión
            $user = ($_SESSION["user"] !== "Invitado") ? $_SESSION["user"]->user : "Invitado";
            $email = ($_SESSION["user"] !== "Invitado") ? $_SESSION["user"]->email : "Invitado";

            // Renderizar la vista "admin_view.twig" con los datos obtenidos
            return $this->renderHTML("admin_view.twig", [
                "allComments" => $data["allComments"],
                "tags" => $data["tags"],
                "user" => $user,
                "email" => $email
            ]);
        }
    }