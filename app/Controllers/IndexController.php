<?php
    namespace App\Controllers;

    use App\Controllers\BaseController;
    include "../lib/lib.php";
    use App\Models\Blog;

    class IndexController extends BaseController {        

        // Método para mostrar la página principal
        public function IndexAction() {
            // Obtener todos los blogs ordenados por fecha de creación descendente
            $data["blogs"] = Blog::orderBy('created', 'desc')->get();

            // Obtener los últimos 5 comentarios
            $data["allComments"] = array_reverse(array_slice(getAllComments($data["blogs"]), -5));

            // Obtener información del usuario de la sesión
            $user = ($_SESSION["user"] !== "Invitado") ? $_SESSION["user"]->user : "Invitado";
            $email = ($_SESSION["user"] !== "Invitado") ? $_SESSION["user"]->email : "Invitado";
            $profile = ($_SESSION["profile"] !== "Invitado") ? $_SESSION["user"]->profile : "Invitado";

            // Obtener las etiquetas
            $data["tags"] = printTags();

            // Renderizar la vista "index_view.twig" con los datos obtenidos
            return $this->renderHTML("index_view.twig", [
                "blogs" => $data["blogs"],
                "allComments" => $data["allComments"],
                "tags" => $data["tags"],
                "profile" => $profile,
                "user" => $user,
                "email" => $email
            ]);
        }

        // Método para mostrar la página "Acerca de"
        public function AboutAction() {
            // Obtener los últimos 5 comentarios
            $data["allComments"] = array_reverse(array_slice(getAllComments(Blog::all()), -5));
            $data["tags"] = printTags();

            // Obtener información del usuario de la sesión
            $user = ($_SESSION["user"] !== "Invitado") ? $_SESSION["user"]->user : "Invitado";
            $email = ($_SESSION["user"] !== "Invitado") ? $_SESSION["user"]->email : "Invitado";
            $profile = ($_SESSION["profile"] !== "Invitado") ? $_SESSION["user"]->profile : "Invitado";

            // Renderizar la vista "about_view.twig" con los datos obtenidos
            return $this->renderHTML("about_view.twig", [
                "allComments" => $data["allComments"],
                "tags" => $data["tags"],
                "profile" => $profile,
                "user" => $user,
                "email" => $email
            ]);
        }

        // Método para mostrar la página de contacto
        public function ContactAction(){
            // Obtener los últimos 5 comentarios
            $data["allComments"] = array_reverse(array_slice(getAllComments(Blog::all()), -5));
            $data["tags"] = printTags();

            // Obtener información del usuario de la sesión
            $user = ($_SESSION["user"] !== "Invitado") ? $_SESSION["user"]->user : "Invitado";
            $email = ($_SESSION["user"] !== "Invitado") ? $_SESSION["user"]->email : "Invitado";
            $profile = ($_SESSION["profile"] !== "Invitado") ? $_SESSION["user"]->profile : "Invitado";

            // Renderizar la vista "contact_view.twig" con los datos obtenidos
            return $this->renderHTML("contact_view.twig", [
                "allComments" => $data["allComments"],
                "tags" => $data["tags"],
                "profile" => $profile,
                "user" => $user,
                "email" => $email
            ]);
        }
    }