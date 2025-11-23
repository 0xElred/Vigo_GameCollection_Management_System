<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    use HasFactory;
    protected $fillable = [
        'Game_name',
        'Publisher',
        'Availability',
        'Description',
        'platform_id',
    ];
    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
