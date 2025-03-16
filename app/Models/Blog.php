<?php
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;
    use App\Models\Comment;

    class Blog extends Eloquent{
        
        protected $table = "blog";

        const CREATED_AT = "created";
        const UPDATED_AT = "updated";

        protected $fillable = ["id", "title", "author", "blog", "image", "tags", "created", "updated"];

        public function comment() {
            return $this->hasMany(Comment::class);
        }

        public function getComments() {
            $comments = [];
            foreach (Blog::find($this->id)->comment as $value2) {
                $comments[] = $value2;
            }

            return $comments;
        }

        public function numComments(){
            return count(Blog::find($this->id)->comment);
        }  
    }