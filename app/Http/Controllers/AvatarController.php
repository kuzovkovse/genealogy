<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AvatarController extends Controller
{
    public function show(Request $request)
    {
        $name = mb_strtoupper($request->get('name', '?'));
        $gender = $request->get('gender');

        // Цвета по полу
        $bg = match ($gender) {
            'male' => '#1e88e5',      // синий
            'female' => '#e91e63',    // розовый
            default => '#9ca3af',     // серый
        };

        $text = '#ffffff';

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200">
    <rect width="200" height="200" rx="24" fill="$bg"/>
    <text x="100" y="120"
          font-size="72"
          text-anchor="middle"
          fill="$text"
          font-family="Arial, sans-serif"
          font-weight="700">
        $name
    </text>
</svg>
SVG;

        return new Response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
