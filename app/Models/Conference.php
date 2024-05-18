<?php

namespace App\Models;


use App\Models\Committee;
use App\Models\ConferenceImage;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Conference extends Model
{
    // use HasFactory;
    // use HasUuids;

    protected $fillable = [
       'name','paperCall', 'updated_deadline', 'original_deadline', 'status', 'accept_noti',
        'email', 'book', 'brochure', 'local_fee', 'foreign_fee', 'conference_date',
        'paper_format', 'topics', 'images'
    ];

    protected $casts = [
        'topics' => 'array',  // Casting topics to array
    ];
    public function committe_members()
    {
        return $this->hasMany(CommitteMember::class);
    }


}
