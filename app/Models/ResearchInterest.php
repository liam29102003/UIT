<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchInterest extends Model
{
    use HasFactory;
    protected $fillable = ['research', 'staff_id'];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
