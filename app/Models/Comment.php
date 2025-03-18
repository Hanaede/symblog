<?php
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class Comment extends Eloquent{
        // Define la tabla asociada a este modelo
        protected $table = "comment";

        // Define los nombres de las columnas de timestamps
        const CREATED_AT = "created";
        const UPDATED_AT = "updated";

        // Define los atributos que se pueden asignar masivamente
        protected $fillable = ["id", "blog_id", "user", "comment", "approved", "created", "updated"];
    }