<?php

namespace App\Services;

use App\Models\Person;

class MemoryProgressService
{
    public function build(Person $person): array
    {
        $score = 0;
        $missing = [];

        // =========================
        // 🧑 ОСНОВНОЕ (30%)
        // =========================

        if ($person->first_name && $person->last_name) {
            $score += 10;
        }

        if ($person->birth_date) {
            $score += 10;
        }

        if ($person->photo) {
            $score += 10;
        } else {
            $missing[] = 'photo';
        }

        // =========================
        // 🕯 СТАТУС ЖИЗНИ (10%)
        // =========================

        if ($person->death_date) {
            $score += 5;
        }

        if ($person->biography) {
            $score += 5;
        } else {
            $missing[] = 'biography';
        }

        // =========================
        // 👨‍👩‍👧 СЕМЬЯ (25%)
        // =========================

        // Родители (через couple_id)
        if ($person->couple_id) {
            $score += 5;
        } else {
            $missing[] = 'parents';
        }

        // Партнёр
        if ($person->couples()->exists()) {
            $score += 5;
        } else {
            $missing[] = 'partner';
        }

        // Дети
        if ($person->children()->exists()) {
            $score += 5;
        } else {
            $missing[] = 'children';
        }

        // Братья / сёстры
        if ($person->couple_id) {
            $siblingsCount = Person::where('couple_id', $person->couple_id)
                ->where('id', '!=', $person->id)
                ->count();

            if ($siblingsCount > 0) {
                $score += 5;
            } else {
                $missing[] = 'siblings';
            }
        }

        // =========================
        // 📸 ПАМЯТЬ (20%)
        // =========================

        if ($person->photos()->exists()) {
            $score += 10;
        } else {
            $missing[] = 'gallery';
        }

        if ($person->events()->exists()) {
            $score += 10;
        } else {
            $missing[] = 'timeline';
        }

        // =========================
        // 🪖 СЛУЖБА (15%)
        // =========================

        if ($person->is_war_participant) {
            $score += 5;

            if ($person->militaryServices()->exists()) {
                $score += 5;

                $docsCount = $person->militaryServices
                    ->flatMap(fn ($s) => $s->documents)
                    ->count();

                if ($docsCount > 0) {
                    $score += 5;
                } else {
                    $missing[] = 'military_documents';
                }
            } else {
                $missing[] = 'military_service';
            }
        }

        return [
            'score'   => min($score, 100),
            'missing' => $missing,
        ];
    }
}
