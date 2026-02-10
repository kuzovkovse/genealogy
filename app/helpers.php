<?php

if (!function_exists('roman')) {
    function roman(int $number): string
    {
        $map = [
            'M'  => 1000,
            'CM' => 900,
            'D'  => 500,
            'CD' => 400,
            'C'  => 100,
            'XC' => 90,
            'L'  => 50,
            'XL' => 40,
            'X'  => 10,
            'IX' => 9,
            'V'  => 5,
            'IV' => 4,
            'I'  => 1,
        ];

        $result = '';

        foreach ($map as $roman => $value) {
            while ($number >= $value) {
                $result .= $roman;
                $number -= $value;
            }
        }

        return $result;
    }

    if (!function_exists('roman')) {
        function roman(int $number): string
        {
            $map = [
                'M'  => 1000,
                'CM' => 900,
                'D'  => 500,
                'CD' => 400,
                'C'  => 100,
                'XC' => 90,
                'L'  => 50,
                'XL' => 40,
                'X'  => 10,
                'IX' => 9,
                'V'  => 5,
                'IV' => 4,
                'I'  => 1,
            ];

            $result = '';

            foreach ($map as $roman => $value) {
                while ($number >= $value) {
                    $result .= $roman;
                    $number -= $value;
                }
            }

            return $result;
        }
    }

    if (!function_exists('familyAuditText')) {
        function familyAuditText(\App\Models\FamilyAuditLog $log): string
        {
            return match ($log->action) {
                'transfer_ownership' => 'передал(а) владение семьёй',
                'change_role'        => 'изменил(а) роль участника',
                'invite_user'        => 'пригласил(а) нового участника',
                'remove_user'        => 'удалил(а) участника из семьи',
                default              => 'выполнил(а) действие',
            };
        }
    }

}
