<?php
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;
    use App\Models\Comment;

    class Blog extends Eloquent{
        // Define la tabla asociada a este modelo
        protected $table = "blog";

        // Define los nombres de las columnas de timestamps
        const CREATED_AT = "created";
        const UPDATED_AT = "updated";

        // Define los atributos que se pueden asignar masivamente
        protected $fillable = ["id", "title", "author", "blog", "image", "tags", "created", "updated"];

         // Define una relación uno a muchos con el modelo Comment
        public function comment() {
            return $this->hasMany(Comment::class);
        }

        // Método para obtener todos los comentarios asociados a esta entrada de blog
        public function getComments() {
            $comments = [];
            foreach (Blog::find($this->id)->comment as $value2) {
                $comments[] = $value2;
            }

            return $comments;
        }

        // Método para contar el número de comentarios asociados a esta entrada de blog
        public function numComments(){
            return count(Blog::find($this->id)->comment);
        }  
    }