<?php
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class Usuario extends Eloquent{
        // Define la tabla asociada a este modelo
        protected $table = "usuario";

       // Define los nombres de las columnas de timestamps
        const CREATED_AT = "created";
        const UPDATED_AT = "updated";

        // Define los atributos que se pueden asignar masivamente
        protected $fillable = ["id", "user", "password", "email", "profile", "created", "updated"];
    }