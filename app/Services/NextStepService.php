<?php

namespace App\Services;

use App\Models\Person;

class NextStepService
{
    public function build(Person $person, array $context): array
    {
        $steps = [];

        /* =========================
         * 1️⃣ Пустая хронология
         ========================= */
        if (($context['timeline_count'] ?? 0) === 0) {
            $steps['timeline'] = [
                'icon' => '📌',
                'text' => 'История жизни пока не содержит событий',
                'action' => [
                    'label' => 'Добавить первое событие',
                    'js'     => 'toggleAddEvent()',
                ],
            ];
        }

        /* =========================
         * 2️⃣ Военная служба без документов
         ========================= */
        if (
            $person->is_war_participant
            && ($context['military_services_count'] ?? 0) > 0
            && ($context['military_documents_count'] ?? 0) === 0
        ) {
            $steps['military'] = [
                'icon' => '📎',
                'text' => 'У этой службы могут быть документы',
                'action' => [
                    'label' => 'Добавить документы службы',
                    'js'     => 'toggleMilitaryDocumentForm()',
                ],
            ];
        }

        /* =========================
         * 3️⃣ Фото есть — событий нет
         ========================= */
        if (
            ($context['photos_count'] ?? 0) > 0
            && ($context['timeline_count'] ?? 0) === 0
        ) {
            if (!isset($steps['timeline'])) {
                $steps['gallery'] = [
                    'icon' => '🕰',
                    'text' => 'Эти фотографии — часть истории',
                    'action' => [
                        'label' => 'Добавить событие в хронологию',
                        'js'     => 'toggleAddEvent()',
                    ],
                ];
            }
        }

        /* =========================
         * 🧩 НОВОЕ — Фото детей
         ========================= */
        foreach ($person->couples as $couple) {

            $children = $couple->children;

            if ($children->isEmpty()) {
                continue;
            }

            $childrenWithoutPhoto = $children->filter(fn ($child) => !$child->photo);

            if ($childrenWithoutPhoto->isEmpty()) {
                continue;
            }

            $steps['children_photos'][$couple->id] = [
                'icon' => '📸',
                'text' => 'Фотографии детей помогают сохранить живую память',
                'action' => [
                    'label' => 'Добавить фото в галерею',
                    'js'     => 'toggleAddLifePhoto()',
                ],
            ];
        }

        return $steps;
    }
}
