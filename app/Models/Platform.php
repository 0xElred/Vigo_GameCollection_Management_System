<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Platform extends Model
{
    use HasFactory;
    protected $fillable = [
        'Platform_name',
    ];
    public function games()
    {
        return $this->hasMany(Game::class);
    }
}
