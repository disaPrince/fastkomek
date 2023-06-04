<?php

namespace App\Telegram;

use App\Models\Service;

class BaseKeyboard
{
    public static function getServiceKeyboardByLanguage($language)
    {
        if ($language === 'ru') {
            $data = Service::query()->pluck('ruName');
            $keyboard = self::doTwoButtonColumn($data, 'ru');
        } else {
            $data = Service::query()->pluck('kzName');
            $keyboard = self::doTwoButtonColumn($data, 'kz');
        }
        return $keyboard;
    }

    public static function getTopicKeyboardByLanguage($text, $language)
    {
        if ($language === 'ru') {
            $data = Service::query()->where('ruName', $text)->first()->topic()->pluck('ruName');
            if (!$data) {
                return false;
            }
            $inlineKeyboard = self::doInlineButtonColumn($data);
        } else {
            $data = Service::query()->where('kzName', $text)->first()->topic()->pluck('kzName');
            if (!$data) {
                return false;
            }
            $inlineKeyboard = self::doInlineButtonColumn($data);
        }
        return $inlineKeyboard;
    }

    public static function getProgrammingLanguage()
    {
        return self::doInlineButtonColumn(['Java', 'PHP', 'JavaScript', 'Python', 'C++', 'Oracle: MySQL, SQL, PL/SQL']);
    }

    public static function getLanguageButton()
    {
        return [['На русском языке 🇷🇺', 'Қазақ тілінде 🇰🇿']];
    }

    protected static function doTwoButtonColumn($array, $language)
    {
        $keys = [];
        $tempKeys = [];
        foreach ($array as $item) {
            $tempKeys[] = $item;
            if (count($tempKeys) === 2) {
                $keys[] = $tempKeys;
                $tempKeys = [];
            }
        }
        if (count($tempKeys) > 0)
            $keys[] = $tempKeys;
        if ($language === 'ru') {
            array_push($keys, ['🔙 На главную']);
        } else {
            array_push($keys, ['🔙 Басты бетке']);
        }
        return $keys;
    }

    protected static function doInlineButtonColumn($array)
    {
        $result = [];
        foreach ($array as $element) {
            array_push(
                $result,
                [['text' => $element, 'callback_data' => $element]],
            );
        }
        return $result;
    }
}