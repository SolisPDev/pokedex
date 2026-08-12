<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PokemonCollection extends Model
{
    protected $fillable = [
        'user_id',
        'pokemon_id',
        'pokemon_name',
        'pokemon_type',
        'custom_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
