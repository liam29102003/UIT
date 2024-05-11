<?php

namespace App\Models;


use App\Models\Committee;
use App\Models\ConferenceImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Conference extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','conference_date','description','topics','general_chair',
        'co_chair','program_chair','paper_sub_guide','sub_deadline','updated_sub_deadline',
        'accept_noti','normal_fee','early_bird_fee','local_fee','sub_email','camera_ready',
        'brochure','book'
    ];

    public function committee()
    {
        return $this->hasMany(Committee::class);
    }

    public function conferenceimage()
    {
        return $this->hasMany(ConferenceImage::class);
    }

}
