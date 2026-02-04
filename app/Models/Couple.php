<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Person;

class Couple extends Model
{
    protected $fillable = [
        'person_1_id',
        'person_2_id',
        'relation_type',
        'married_at',
        'divorced_at',
    ];

    public function person1()
    {
        return $this->belongsTo(Person::class, 'person_1_id');
    }

    public function person2()
    {
        return $this->belongsTo(Person::class, 'person_2_id');
    }

    /**
     * Дети этого брака
     * people.couple_id → couples.id
     */
    public function children()
    {
        return $this->hasMany(Person::class, 'couple_id');
    }
}
