<?php

namespace App\Models;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;
    protected $fillable = ['publications', 'staff_id'];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
