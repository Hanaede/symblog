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

        public function AddAction($request) {
            $responseMessage = null;
        
            $postData = $request->getParsedBody();
            $blogValidator = v::key('title', v::stringType()->notEmpty())
                ->key('desc', v::stringType()->notEmpty())
                ->key('tags', v::stringType()->notEmpty())
                ->key('author', v::stringType()->notEmpty());
        
            try {
                $blogValidator->assert($postData);
                $blogData = [
                    'title' => $postData['title'],
                    'author' => $postData['author'],
                    'blog' => $postData['desc'],
                    'tags' => $postData['tags'],
                    'image' => null
                ];
        
                //carga de ficheros
                $files = $request->getUploadedFiles();
                $imagen = $files['image'];
                if ($imagen->getError() == UPLOAD_ERR_OK) {
                    $fileName = $imagen->getClientFilename();
                    $fileName = uniqid() . $fileName;
                    $imagen->moveTO("../public/img/$fileName");
                    $blogData['image'] = $fileName;
                }
        
                $blog = Blog::create($blogData);
                $blog->save();
                $responseMessage = "Saved";
            } catch (\Exception $e) {
                $responseMessage = $e->getMessage();
            }
            $data["allComments"] = array_reverse(array_slice(getAllComments(Blog::all()), -5));
            $data["tags"] = printTags();
            $profile = ($_SESSION["profile"] !== "Invitado") ? $_SESSION["user"]->profile : "Invitado";
        
            // Redireccionar a una página diferente después de agregar el blog
            $cookie = setcookie("newBlog", true, time() + 60, "/");
        
            return $this->renderHTML("addBlog_view.twig", [
                "allComments" => $data["allComments"],
                "tags" => $data["tags"],
                "profile" => $profile,
                "cookie" => $cookie,
                "responseMessage" => $responseMessage
            ]);
        }

        public function NewAction() {
        
            $data["allComments"] = array_reverse(array_slice(getAllComments(Blog::all()), -5));
            $data["tags"] = printTags();
            $profile = ($_SESSION["profile"] !== "Invitado") ? $_SESSION["user"]->profile : "Invitado";

            return $this->renderHTML("addBlog_view.twig", [
                "allComments" => $data["allComments"],
                "tags" => $data["tags"],
                "profile" => $profile
            ]);
        }

        public function AddCommentAction($request) {
            $responseMessage = null;
            
            $postData = $request->getParsedBody();
            $commentValidator = v::key('comment', v::stringType()->notEmpty());

            try {
                $commentValidator->assert($postData);
                $comment = Comment::create([
                    'blog_id' => $_GET["id"],
                    'user' => $_SESSION['user']->user,
                    'comment' => $postData['comment'],
                    'approved' => 1
                ]);
                $responseMessage = "Saved";
                $comment->save();
            } catch (\Exception $e) {
                $responseMessage = $e->getMessage();
            }
             return new RedirectResponse("/show?id=".$_GET["id"]."");
        }

        public function ShowAction($request) {
            $data["blog"] = Blog::find($_GET["id"]);
            $data["allComments"] = array_reverse(array_slice(getAllComments(Blog::all()), -5));
            $data["tags"] = printTags();

            $user = ($_SESSION["user"] !== "Invitado") ? $_SESSION["user"]->user : "Invitado";
            $email = ($_SESSION["user"] !== "Invitado") ? $_SESSION["user"]->email : "Invitado";
            $profile = ($_SESSION["profile"] !== "Invitado") ? $_SESSION["user"]->profile : "Invitado";

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