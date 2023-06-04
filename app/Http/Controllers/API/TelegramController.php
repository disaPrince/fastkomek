<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Clients;
use Illuminate\Http\Request;
use PhpParser\Builder\TraitUse;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Keyboard\Keyboard;
use App\Telegram\Commands\StartCommand;
use Illuminate\Support\Facades\Log;
use App\Telegram\BaseKeyboard;
use App\Models\TelegramRequestLog;
use App\Models\News;
use App\Models\NewsReactions;

class TelegramController extends Controller
{
    public function start(Request $request)
    {
        $telegramId = $request->telegram_id;
        $telegramUsername = $request->user_name;
        return StartCommand::excuteCommand($telegramId, $telegramUsername);
    }

    public function message(Request $request)
    {
        try {
            $telegramRequestLog = TelegramRequestLog::query()->where('telegramId', $request->telegram_id)->first();

            if ($request->message === '🔙 На главную' || $request->message === '🔙 Басты бетке') {
                return $this->start($request);
            }

            if ($telegramRequestLog->command) {
                if ($telegramRequestLog->command === 'chooseLanguage') {

                    $inlineKeyboard = BaseKeyboard::getTopicKeyboardByLanguage($request->message, json_decode($telegramRequestLog->json_data, true)['language']);

                    if (!$inlineKeyboard) {
                        if (json_decode($telegramRequestLog->json_data, true)['language'] === 'ru') {
                            $keyboard = BaseKeyboard::getServiceKeyboardByLanguage('ru');
                            $text = 'Пока эта категория пуста, пожалуйста выберите другую категорию:';
                        } else {
                            $keyboard = BaseKeyboard::getServiceKeyboardByLanguage('kz');
                            $text = 'Әзірге бұл санат бос, басқа санатты таңдауыңызды өтінемін';
                        }
                        return $this->sendButtonMessage($text, $keyboard, $request->telegram_id);
                    }

                    if (json_decode($telegramRequestLog->json_data, true)['language'] === 'ru') {
                        $text = 'Выберите интересующую вас категорию: ⬇️';
                    } else {
                        $text = 'Сізге керек санатты таңдаңыз: ⬇️';
                    }

                    $json_data = json_decode($telegramRequestLog->json_data, true);
                    $json_data['category'] = $request->message;


                    $this->telegramRequestLogUpdate($request->telegram_id, $json_data, 'chooseLanguage');
                    return $this->sendInlineMessage($text, $inlineKeyboard, $request->telegram_id);
                } elseif ($telegramRequestLog->command === 'final') {
                    if (!array_key_exists('description', json_decode($telegramRequestLog->json_data, true))) {
                        if (json_decode($telegramRequestLog->json_data, true)['language'] === 'ru') {
                            $text = '👤 Ваше имя:';
                            $keyboard = [['🔙 На главную']];
                        } else {
                            $text = '👤 Сіздің атыңыз:';
                            $keyboard = [['🔙 Басты бетке']];
                        }
                        $json_data = json_decode($telegramRequestLog->json_data, true);
                        $json_data['description'] = $request->message;
                        $this->telegramRequestLogUpdate($request->telegram_id, $json_data, 'final');
                        return $this->sendButtonMessage($text, $keyboard, $request->telegram_id);
                    } elseif (!array_key_exists('name', json_decode($telegramRequestLog->json_data, true))) {
                        if (json_decode($telegramRequestLog->json_data, true)['language'] === 'ru') {
                            $text = 'Укажите свой контактный номер телефона в виде: +7XXXXXXXXXX';
                            $keyboard = [['🔙 На главную']];
                        } else {
                            $text = 'Байланыс телефон нөміріңізді келесідей көрсетіңіз: +7XXXXXXXXXX';
                            $keyboard = [['🔙 Басты бетке']];
                        }
                        $json_data = json_decode($telegramRequestLog->json_data, true);
                        $json_data['name'] = $request->message;
                        $this->telegramRequestLogUpdate($request->telegram_id, $json_data, 'final');
                        return $this->sendButtonMessage($text, $keyboard, $request->telegram_id);
                    } elseif (!array_key_exists('phone', json_decode($telegramRequestLog->json_data, true))) {
                        if (!is_numeric(substr($request->message, 1)) || strlen($request->message) > 12 || strlen($request->message) < 11) {
                            if (json_decode($telegramRequestLog->json_data, true)['language'] === 'ru') {
                                $text = 'Введите корректный номер телефона:';
                                $keyboard = [['🔙 На главную']];
                            } else {
                                $text = 'Дұрыс телефон нөмірін енгізіңіз:';
                                $keyboard = [['🔙 Басты бетке']];
                            }
                            return $this->sendButtonMessage($text, $keyboard, $request->telegram_id);
                        }

                        if (json_decode($telegramRequestLog->json_data, true)['language'] === 'ru') {
                            $text = '⏳ Дедлайн/Срок исполнения:';
                            $keyboard = [['🔙 На главную']];
                        } else {
                            $text = '⏳ Мерзім:';
                            $keyboard = [['🔙 Басты бетке']];
                        }
                        $json_data = json_decode($telegramRequestLog->json_data, true);
                        $json_data['phone'] = $request->message;
                        $this->telegramRequestLogUpdate($request->telegram_id, $json_data, 'final');
                        return $this->sendButtonMessage($text, $keyboard, $request->telegram_id);
                    } elseif (!array_key_exists('deadline', json_decode($telegramRequestLog->json_data, true))) {
                        if (json_decode($telegramRequestLog->json_data, true)['language'] === 'ru') {
                            $text = "Ваша заявка успешно создана и находится в обработке. ✅ \n\nНаши сотрудники в ближайшее время обязательно свяжутся с Вами 📞 \n<b>Среднее ожидание:</b> 10-25 мин. \n\n✨<b>FastKomek</b> всегда готов вам помочь✨";
                            $keyboard = [['🔙 На главную']];
                        } else {
                            $text = "Сіздің өтінішіңіз сәтті жасалды және өңделуде. ✅ \n\nБіздің қызметкерлер жақын арада сізбен міндетті түрде байланысады 📞 \n<b>Орташа күту уақыты:</b> 10-25 мин. \n\n✨<b>FastKomek</b> сізге әрқашан көмектесуге дайын✨";
                            $keyboard = [['🔙 Басты бетке']];
                        }
                        $json_data = json_decode($telegramRequestLog->json_data, true);
                        $json_data['deadline'] = $request->message;
                        $client = Clients::query()->updateOrCreate(
                            ['telegramId' => $request->telegram_id],
                            [
                                'telegramId' => $request->telegram_id,
                                'name' => json_decode($telegramRequestLog->json_data, true)['name'],
                                'phone' => json_decode($telegramRequestLog->json_data, true)['phone'],
                                'telegramUsername' => $telegramRequestLog->telegramUsername,
                            ]
                        );
                        $application = Application::query()->create([
                            'client_id' => $client['id'],
                            'language' => json_decode($telegramRequestLog->json_data, true)['language'] === 'ru' ? 'На русском' : "Қазақша",
                            'category' => json_decode($telegramRequestLog->json_data, true)['category'],
                            'topic' => json_decode($telegramRequestLog->json_data, true)['topic'],
                            'subTopic' => json_decode($telegramRequestLog->json_data, true)['programmingLanguage'] ?? "",
                            'description' => json_decode($telegramRequestLog->json_data, true)['description'],
                            'deadline' => $request->message,
                            'status' => true
                        ]);
                        $this->telegramRequestLogUpdate($request->telegram_id, ['language' => json_decode($telegramRequestLog->json_data, true)['language']], 'chooseLanguage');
                        $this->sendMessage($text, $request->telegram_id);
                        #      1618342840 , 513443978
                        $text = "\n\nЗаявка № $application->id " .
                            "\n\nИмя: " . $client['name'] .
                            "\nНомер: " . $client['phone'] .
                            "\nАккаунт: https://t.me/" . $client['telegramUsername'] .
                            "\nЯзык: $application->language " .
                            "\nКатегория: $application->category" .
                            "\nТема: $application->topic" .
                            "\nПодтема: $application->subTopic" .
                            "\nОписание: $application->description" .
                            "\nДедлайн: $application->deadline" .
                            "\nСтатус: Активная" .
                            "\nДата обращения: $application->created_at";
                        return $this->sendButtonMessage($text, $keyboard, 513443978);
                    }
                }
            } else {
                if ($request->message === "На русском языке 🇷🇺") {
                    $keyboard = BaseKeyboard::getServiceKeyboardByLanguage('ru');
                    $text = "Пожалуйста, выберите нужный вариант: ⬇️";
                    $json_data = json_decode($telegramRequestLog->json_data, true);
                    $json_data['language'] = 'ru';
                    $this->telegramRequestLogUpdate($request->telegram_id, $json_data, 'chooseLanguage');
                    return $this->sendButtonMessage($text, $keyboard, $request->telegram_id);
                } elseif ($request->message === "Қазақ тілінде 🇰🇿") {
                    $keyboard = BaseKeyboard::getServiceKeyboardByLanguage('kz');
                    $text = "Керек нұсқаны таңдаңыз: ⬇️";
                    $json_data = json_decode($telegramRequestLog->json_data, true);
                    $json_data['language'] = 'kz';
                    $this->telegramRequestLogUpdate($request->telegram_id, $json_data, 'chooseLanguage');
                    return $this->sendButtonMessage($text, $keyboard, $request->telegram_id);
                }
                return true;
            }
            return true;
        } catch (\Exception $e) {
            Log::channel('telegram')->debug('TelegramController_Message: ' . $e->getMessage());
        }
    }


    public function callback(Request $request)
    {
        try {
            $telegramRequestLog = TelegramRequestLog::query()->where('telegramId', $request->telegram_id)->first();
            if ($telegramRequestLog->command === 'chooseLanguage') {
                if ($request->callback === 'Помощь по программированию' || $request->callback === 'Бағдарламалау бойынша көмек') {
                    $inlineKeyboard = BaseKeyboard::getProgrammingLanguage();
                    if (json_decode($telegramRequestLog->json_data, true)['language'] === 'ru') {
                        $text = 'Выберите язык программировании, по которому у вас есть вопросы: ⬇️';
                    } else {
                        $text = 'Сұрақтарыңыз бар бағдарламалау тілін таңдаңыз: ⬇️';
                    }
                    $json_data = json_decode($telegramRequestLog->json_data, true);
                    $json_data['topic'] = $request->callback;
                    $this->telegramRequestLogUpdate($request->telegram_id, $json_data, 'chooseProgrammingLanguage');
                    return $this->sendInlineMessage($text, $inlineKeyboard, $request->telegram_id);
                } else {

                    $json_data = json_decode($telegramRequestLog->json_data, true);
                    $json_data['topic'] = $request->callback;

                    $this->telegramRequestLogUpdate($request->telegram_id, $json_data, 'final');
                    if (json_decode($telegramRequestLog->json_data, true)['language'] === 'ru') {
                        $text = 'Поле для дополнительных описании вашей заявки:';
                        $keyboard = [['🔙 На главную']];
                    } else {
                        $text = 'Сіздің өтінішіңіздің сипаттамасы:';
                        $keyboard = [['🔙 Басты бетке']];
                    }
                    return $this->sendButtonMessage($text, $keyboard, $request->telegram_id);
                }
            } elseif ($telegramRequestLog->command === 'chooseProgrammingLanguage') {
                $json_data = json_decode($telegramRequestLog->json_data, true);
                $json_data['programmingLanguage'] = $request->callback;

                $this->telegramRequestLogUpdate($request->telegram_id, $json_data, 'final');

                if (json_decode($telegramRequestLog->json_data, true)['language'] === 'ru') {
                    $text = 'Поле для дополнительных описании вашей заявки:';
                    $keyboard = [['🔙 На главную']];
                } else {
                    $text = 'Сіздің өтінішіңіздің сипаттамасы:';
                    $keyboard = [['🔙 Басты бетке']];
                }
                return $this->sendButtonMessage($text, $keyboard, $request->telegram_id);
            } else {
                $pieces = explode("#poll", $request->callback);
                $newsList = News::where('id', $pieces[1])->get();
                foreach ($newsList as $news) {
                    $existingReaction = NewsReactions::where('telegramId', $request->telegram_id)
                        ->where('news_id', $news->id)
                        ->where('reaction', $pieces[0])
                        ->first();
                    if (!$existingReaction) {
                        $news_reactions = new NewsReactions();
                        $news_reactions->reaction = $pieces[0];
                        $news_reactions->telegramId = $request->telegram_id;
                        $news_reactions->news_id = $news->id;
                        $news_reactions->save();
                        return $this->sendMessage('Спасибо за вашу оценку', $request->telegram_id);
                    }
                }
                return true;
            }
        } catch (\Exception $e) {
            Log::channel('telegram')->debug('TelegramController_Callback: ' . $e->getMessage());
        }
    }
    protected function sendButtonMessage($text, $keyboard, $telegramId)
    {
        $reply_markup = Keyboard::make(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true]);
        return Telegram::sendMessage([
            'chat_id' => $telegramId,
            'text' => $text,
            'reply_markup' => $reply_markup,
            'parse_mode' => 'HTML'
        ]);
    }

    protected function sendInlineMessage($text, $keyboard, $telegramId)
    {
        $reply_markup = Keyboard::make(['inline_keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true]);
        return Telegram::sendMessage([
            'chat_id' => $telegramId,
            'text' => $text,
            'reply_markup' => $reply_markup,
            'parse_mode' => 'HTML'
        ]);
    }

    protected function sendMessage($text, $telegramId)
    {
        return Telegram::sendMessage([
            'chat_id' => $telegramId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);
    }

    protected function telegramRequestLogUpdate($telegramId, $json_data, $command)
    {
        return TelegramRequestLog::query()->updateOrCreate(
            ['telegramId' => $telegramId],
            [
                'telegramId' => $telegramId,
                'json_data' => $json_data,
                'command' => $command
            ]
        );
    }
}