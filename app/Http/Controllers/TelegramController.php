<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();

        if (!isset($data['message'])) {
            return response()->json(['ok' => true]);
        }

        $chatId = $data['message']['chat']['id'];
        $username = $data['message']['from']['username'] ?? null;
        $text = $data['message']['text'] ?? '';

        // Команда /start
        if (str_starts_with($text, '/start')) {

            $this->sendMessage($chatId, "👋 Добро пожаловать в ПомниКорни!\n\nВведите код подключения из вашего профиля.");

            return response()->json(['ok' => true]);
        }

        // Иначе считаем что это код подключения
        $user = User::where('telegram_connect_code', $text)->first();

        if ($user) {
            $user->update([
                'telegram_id' => $chatId,
                'telegram_username' => $username,
                'telegram_connect_code' => null,
            ]);

            $this->sendMessage($chatId, "✅ Telegram успешно подключён к вашему аккаунту!");
        } else {
            $this->sendMessage($chatId, "❌ Неверный код подключения.");
        }

        return response()->json(['ok' => true]);
    }

    private function sendMessage($chatId, $text)
    {
        $token = config('services.telegram.bot_token');

        file_get_contents("https://api.telegram.org/bot{$token}/sendMessage?" . http_build_query([
                'chat_id' => $chatId,
                'text' => $text,
            ]));
    }
}
