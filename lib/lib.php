<?php
    use App\Models\Blog;

    function getAllTags() {
        $allTags = [];

        // Recorre todos los blogs
        foreach (Blog::all() as $blog) {
            //  cadena de etiquetas separadas por comas
            foreach (explode(", ", $blog->tags) as $tag) {
                // Añade la etiqueta al array si no está ya presente
                if (!in_array($tag, $allTags)) $allTags[] = $tag;
            }
        }

        return $allTags;
    }

    function countTag($tag) {
        $count = 0;
        // Recorre todos los blogs
        foreach (Blog::all() as $blog) {
            // Incrementa el contador si la etiqueta está presente en el blog
            if (in_array($tag, explode(", ", $blog->tags)))  $count++;
        }
        return $count;
    }

    function printTags() {
        $tags = "";
        // Recorre todas las etiquetas
        foreach (getAllTags() as $tag) {
            // Genera un <span> con una clase basada en la frecuencia de la etiqueta
            if (countTag($tag) >= 5) {
                $tags .= "<span class=\"weight-5\">".$tag."</span>";
            } else $tags .= "<span class=\"weight-".countTag($tag)."\">".$tag."</span>";            
        }

        return $tags;
    }

    function getAllComments($blogs) {
        $allComments = [];
        // Recorre todos los blogs
        foreach ($blogs as $blog) {
            // Recorre todos los comentarios del blog
            foreach ($blog->comment as $comment) {
                // Añade el comentario al array con información adicional
                $allComments[] = [
                    'comment' => $comment->comment,
                    'user' => $comment->user,
                    'created' => $comment->created,
                    'blogId' => $blog->id,
                    'blogTitle' => $blog->title,
                ];
            }
        }

        // Ordena los comentarios por fecha en orden descendente
        usort($allComments, function ($a, $b) {
            return strtotime($a['created']) - strtotime($b['created']);
        });

        return $allComments;
    }