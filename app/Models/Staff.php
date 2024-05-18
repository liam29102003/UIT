<?php

namespace App\Models;

use App\Models\Subject;
use App\Models\Publication;
use App\Models\ResearchInterest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;
    protected $fillable=['image','name','position','biography','education'];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'staff_subject');
    }

    public function publications()
    {
        return $this->hasMany(Publication::class);
    }

    public function researchInterests()
    {
        return $this->hasMany(ResearchInterest::class);
    }
}
