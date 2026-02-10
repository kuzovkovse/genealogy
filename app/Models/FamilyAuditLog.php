<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\KinshipService;

class FamilyAuditLog extends Model
{
    protected $table = 'family_audit_logs';

    protected $fillable = [
        'family_id',
        'actor_user_id',
        'target_user_id',
        'action',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /* ----------------------------
     | Relations
     |---------------------------- */

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /* ----------------------------
     | Names
     |---------------------------- */

    public function actorName(): string
    {
        return $this->actor?->name ?? 'Система';
    }

    public function personId(): ?int
    {
        return $this->meta['person_id'] ?? null;
    }

    public function personName(): string
    {
        $name = $this->meta['person_name'] ?? null;

        if (is_string($name) && trim($name) !== '') {
            return $name;
        }

        if ($this->personId()) {
            $person = Person::find($this->personId());
            if ($person) {
                return trim($person->full_name) ?: 'без имени';
            }
        }

        return 'без имени';
    }

    /* ----------------------------
     | UX helpers
     |---------------------------- */

    public function isSilent(): bool
    {
        return $this->icon() === null;
    }

    public function icon(): ?string
    {
        return match ($this->action) {
            'transfer_ownership' => '👑',
            'person_created'     => '➕',
            'person_updated'     => '✏️',
            'person_deleted'     => '🗑️',
            default              => null,
        };
    }

    /* ----------------------------
     | TITLE (🔥 ГЛАВНОЕ)
     |---------------------------- */

    public function title(): string
    {
        if ($this->action === 'person_updated') {
            $kinship = $this->resolveKinshipLabel();

            return $kinship
                ? "{$this->actorName()} обновил(а) данные {$kinship}"
                : "{$this->actorName()} обновил(а) данные человека «{$this->personName()}»";
        }

        return match ($this->action) {
            'person_created' =>
            "{$this->actorName()} добавил(а) человека «{$this->personName()}»",

            'person_deleted' =>
            "{$this->actorName()} удалил(а) человека «{$this->personName()}»",

            'transfer_ownership' =>
            "{$this->actorName()} передал(а) владение семьёй",

            default =>
            "{$this->actorName()} выполнил(а) действие",
        };
    }

    /**
     * 🧬 Определить родство относительно текущего пользователя
     */
    protected function resolveKinshipLabel(): ?string
    {
        if (!$this->personId() || !Auth::check()) {
            return null;
        }

        $target = Person::find($this->personId());
        if (!$target) {
            return null;
        }

        // Person текущего пользователя
        $me = Person::where('user_id', Auth::id())->first();
        if (!$me) {
            return null;
        }

        /** @var KinshipService $kinship */
        $kinship = app(KinshipService::class);

        $ancestors = $kinship->getAncestors($me, 3);

        $match = $ancestors->first(
            fn ($item) => $item['person']->id === $target->id
        );

        if (!$match) {
            return null;
        }

        return $this->kinshipWord(
            depth: $match['depth'],
            gender: $target->gender
        );
    }

    /**
     * Превращаем depth + пол → слово
     */
    protected function kinshipWord(int $depth, ?string $gender): ?string
    {
        return match ($depth) {
            1 => $gender === 'female' ? 'матери' : 'отца',
            2 => $gender === 'female' ? 'бабушки' : 'деда',
            3 => $gender === 'female' ? 'прабабушки' : 'прадеда',
            default => null,
        };
    }

    /* ----------------------------
     | Changes
     |---------------------------- */

    public function changesText(): ?string
    {
        $changes = $this->meta['changes'] ?? null;

        if (!$changes || !is_array($changes)) {
            return null;
        }

        $labels = $this->fieldLabels();
        $parts  = [];

        foreach ($changes as $field => $change) {
            if (!isset($change['old'], $change['new'])) {
                continue;
            }

            $label = $labels[$field] ?? $field;

            $old = $this->formatValue($field, $change['old']);
            $new = $this->formatValue($field, $change['new']);

            if ($old === $new) {
                continue;
            }

            $parts[] = "{$label}: {$old} → {$new}";
        }

        return $parts ? implode('<br>', $parts) : null;
    }

    protected function fieldLabels(): array
    {
        return [
            'birth_date' => 'Дата рождения',
            'death_date' => 'Дата смерти',
            'first_name' => 'Имя',
            'last_name'  => 'Фамилия',
            'patronymic' => 'Отчество',
            'gender'     => 'Пол',
        ];
    }

    protected function formatValue(string $field, mixed $value): string
    {
        if (!$value) {
            return '—';
        }

        if (in_array($field, ['birth_date', 'death_date'], true)) {
            try {
                return Carbon::parse($value)->translatedFormat('d F Y');
            } catch (\Throwable) {
                return (string) $value;
            }
        }

        return (string) $value;
    }

    /* ----------------------------
     | New marker
     |---------------------------- */

    public function isNewForUser(): bool
    {
        $lastSeen = session('family_history_last_seen');

        return !$lastSeen || $this->created_at->gt($lastSeen);
    }
}
