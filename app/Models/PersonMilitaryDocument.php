<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonMilitaryDocument extends Model
{
    protected $fillable = [
        'person_military_service_id',
        'type',           // pdf | image
        'title',
        'file_path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    /* =========================
     * 🔗 Связи
     * ========================= */

    public function service(): BelongsTo
    {
        return $this->belongsTo(PersonMilitaryService::class, 'person_military_service_id');
    }

    /* =========================
     * 🧠 Хелперы
     * ========================= */

    public function isPdf(): bool
    {
        return $this->type === 'pdf';
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function url(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
