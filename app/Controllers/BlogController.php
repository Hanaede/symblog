<?php
namespace App\Controllers;

include "../lib/lib.php";
use App\Controllers\BaseController;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Response\HTMLResponse;
use App\Models\Blog;
use App\Models\Comment;
use Respect\Validation\Validator as v;

class BlogController extends BaseController {

    // Método para agregar un nuevo blog
    public function AddAction($request) {
        $responseMessage = null;
        
        // Obtener los datos del formulario
        $postData = $request->getParsedBody();
        
        // Validar los datos del formulario
        $blogValidator = v::key('title', v::stringType()->notEmpty())
            ->key('desc', v::stringType()->notEmpty())
            ->key('tags', v::stringType()->notEmpty())
            ->key('author', v::stringType()->notEmpty());
        
        try {
            // Validar los datos
            $blogValidator->assert($postData);
            
            // Preparar los datos del blog
            $blogData = [
                'title' => $postData['title'],
                'author' => $postData['author'],
                'blog' => $postData['desc'],
                'tags' => $postData['tags'],
                'image' => null
            ];
        
            // Manejar la carga de archivos
            $files = $request->getUploadedFiles();
            $imagen = $files['image'];
            if ($imagen->getError() == UPLOAD_ERR_OK) {
                $fileName = $imagen->getClientFilename();
                $fileName = uniqid() . $fileName;
                $imagen->moveTO("../public/img/$fileName");
                $blogData['image'] = $fileName;
            }
        
            // Crear y guardar el blog
            $blog = Blog::create($blogData);
            $blog->save();
            $responseMessage = "Saved";
        } catch (\Exception $e) {
            $responseMessage = $e->getMessage();
        }
        
        // Obtener los comentarios y etiquetas para la vista
        $data["allComments"] = array_reverse(array_slice(getAllComments(Blog::all()), -5));
        $data["tags"] = printTags();
        $profile = ($_SESSION["profile"] !== "Invitado") ? $_SESSION["user"]->profile : "Invitado";
        
        // Redireccionar a una página diferente después de agregar el blog
        $cookie = setcookie("newBlog", true, time() + 60, "/");
        
        // Renderizar la vista con los datos
        return $this->renderHTML("addBlog_view.twig", [
            "allComments" => $data["allComments"],
            "tags" => $data["tags"],
            "profile" => $profile,
            "cookie" => $cookie,
            "responseMessage" => $responseMessage
        ]);
    }

    // Método para mostrar la página de creación de un nuevo blog
    public function NewAction() {
        // Obtener los comentarios y etiquetas para la vista
        $data["allComments"] = array_reverse(array_slice(getAllComments(Blog::all()), -5));
        $data["tags"] = printTags();
        $profile = ($_SESSION["profile"] !== "Invitado") ? $_SESSION["user"]->profile : "Invitado";

        // Renderizar la vista con los datos
        return $this->renderHTML("addBlog_view.twig", [
            "allComments" => $data["allComments"],
            "tags" => $data["tags"],
            "profile" => $profile
        ]);
    }

    // Método para agregar un comentario a un blog
    public function AddCommentAction($request) {
        $responseMessage = null;
        
        // Obtener los datos del formulario
        $postData = $request->getParsedBody();
        
        // Validar los datos del formulario
        $commentValidator = v::key('comment', v::stringType()->notEmpty());

        try {
            // Validar los datos
            $commentValidator->assert($postData);
            
            // Crear y guardar el comentario
            $comment = Comment::create([
                'blog_id' => $_GET["id"],
                'user' => $_SESSION['user']->user??'Invitado',
                'comment' => $postData['comment'],
                'approved' => 1
            ]);
            $responseMessage = "Saved";
            $comment->save();
        } catch (\Exception $e) {
            $responseMessage = $e->getMessage();
        }
        
        // Redireccionar a la página del blog
        return new RedirectResponse("/show?id=".$_GET["id"]."");
    }

    // Método para mostrar un blog específico
    public function ShowAction($request) {
        // Obtener los datos del blog, comentarios y etiquetas
        $data["blog"] = Blog::find($_GET["id"]);
        $data["allComments"] = array_reverse(array_slice(getAllComments(Blog::all()), -5));
        $data["tags"] = printTags();

        // Obtener los datos del usuario
        $user = ($_SESSION["user"] !== "Invitado") ? $_SESSION["user"]->user : "Invitado";
        $email = ($_SESSION["user"] !== "Invitado") ? $_SESSION["user"]->email : "Invitado";
        $profile = ($_SESSION["profile"] !== "Invitado") ? $_SESSION["user"]->profile : "Invitado";

        // Renderizar la vista con los datos
        return $this->renderHTML("show_view.twig", [
            "blog" => $data["blog"],
            "allComments" => $data["allComments"],
            "tags" => $data["tags"],
            "comments" => array_reverse($data["blog"]->getComments()),
            "numComments" => count($data["blog"]->getComments()),
            "profile" => $profile,
            "user" => $user,
            "email" => $email
        ]);
    }
}