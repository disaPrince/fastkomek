<?php

namespace App\Telegram\Commands;

use Illuminate\Console\Command;
use App\Models\TelegramRequestLog;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Keyboard\Keyboard;
use App\Telegram\BaseKeyboard;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StartCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'StartCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
    }

    public static function excuteCommand($telegramId, $telegramUsername)
    {
        try {
            TelegramRequestLog::query()->updateOrCreate(
                ['telegramId' => $telegramId],
                [
                    'telegramId' => $telegramId,
                    'telegramUsername' => $telegramUsername,
                    'json_data' => json_encode([]),
                    'command' => null
                ]
            );

            $keyboards = BaseKeyboard::getLanguageButton();
            $reply_markup = Keyboard::make(['keyboard' => $keyboards, 'resize_keyboard' => true, 'one_time_keyboard' => true]);
            $time = Carbon::now()->format('H:i');
            $text = [];
            if ($time > '05:00' && $time <= '10:00') {
                $text = ['ru' => 'Доброе утро 🌕', 'kz' => 'Қайырлы таң 🌕'];
            } elseif ($time > '10:00' && $time <= '17:00') {
                $text = ['ru' => 'Добрый день  🌞', 'kz' => 'Қайырлы күн 🌞'];
            } else {
                $text = ['ru' => 'Добрый вечер 🌙', 'kz' => 'Қайырлы кеш 🌙'];
            }

            Telegram::sendMessage([
                'chat_id' => $telegramId,
                'text' => $text['kz'] . "\nМенің атым <b>FastKomek</b> 💡 \nҚай тілде ақпарат алғыныз келеді? " . "\n\n" . $text['ru'] . "\nМеня зовут <b>FastKomek</b> 💡 \nНа каком языке хотите получить информацию ? ",
                'reply_markup' => $reply_markup,
                'parse_mode' => 'HTML'
            ]);
        } catch (\Exception $e) {
            Log::channel('telegram')->debug('/startCommand ' . $e->getMessage());
        }
    }
}