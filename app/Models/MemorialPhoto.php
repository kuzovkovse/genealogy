<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemorialPhoto extends Model
{
    protected $fillable = [
        'person_id',
        'image_path',
        'title',
        'description',
        'taken_year',
        'created_by',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
