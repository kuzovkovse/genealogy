<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonPhoto extends Model
{
    protected $fillable = [
        'person_id',
        'image_path',
        'title',
        'description',
        'taken_year',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
