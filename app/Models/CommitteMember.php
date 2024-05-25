<?php

namespace App\Models;

use App\Models\Conference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommitteMember extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'rank', 'position','speaker_type','member_type', 'chair_type', 'nation', 'university','conference_id'
    ];
    public function conference()
    {
        $this->belongsTo(Conference::class);
    }
}
