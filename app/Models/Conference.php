<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    use HasFactory;
    protected $fillable= ['paperCall', 'updated_deadline', 'original_deadline', 'status', 'accept_noti', 'email','book','brochure','local_fee','foreign_fee','conference_date','paper_format'];
}
