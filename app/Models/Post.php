<?php

namespace App\Models;

use App\Models\NewsImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;
    
    use SoftDeletes;

    protected $dates = ['deleted_at']; // Specify the column used for soft deletes
    protected $fillable =['title','body','admin_id']; // Specify fillable fields
    public function images()
    {
        return $this->hasMany(NewsImage::class);
    }
}
