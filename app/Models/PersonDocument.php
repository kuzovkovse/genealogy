<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonDocument extends Model
{
    protected $fillable = [
        'person_id',
        'title',
        'type',
        'year',
        'file_path',
        'description',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function isPdf(): bool
    {
        return str_ends_with($this->file_path, '.pdf');
    }
}
