<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
    use HasFactory;

    protected $table = 'likes';

    protected $fillable = [
        'user_id',
        'receta_id',
        'titulo_receta',
        'imagen_url',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
