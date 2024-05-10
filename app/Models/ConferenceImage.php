<?php

namespace App\Models;

use App\Models\Conference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConferenceImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'name','conference_id'
    ];

    public function conference(){
        $this->belongsTo(Conference::class);
    }
}
