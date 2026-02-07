<?php

namespace App\Services;

use App\Models\Person;

class NextStepService
{
    /**
     * Возвращает массив подсказок по ключам блоков.
     * Ключ = место показа (timeline / military / gallery)
     */
    public function build(Person $person, array $context): array
    {
        $steps = [];

        // 1️⃣ Пустая хронология
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

        // 2️⃣ Военная служба БЕЗ документов
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

        // 3️⃣ Есть фото, но нет событий
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

        return $steps;
    }
}
