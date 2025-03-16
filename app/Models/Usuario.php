<?php
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class Usuario extends Eloquent{
        protected $table = "usuario";

        const CREATED_AT = "created";
        const UPDATED_AT = "updated";

        protected $fillable = ["id", "user", "password", "email", "profile", "created", "updated"];
    }