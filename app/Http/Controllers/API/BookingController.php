<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TelegramRequestLog;
use Illuminate\Support\Facades\Log;
use App\Services\DateService;
use App\Models\BookRecord;

class BookingController extends Controller
{
    public function showWeek(Request $request) {
        $chatId = $request['chatId'];
        $type = $request['type'];
        $dateService = app(DateService::class);
          $telegramRequestLog = TelegramRequestLog::where('telegramId', $chatId)->first();
          Log::debug($telegramRequestLog->json_data);
          TelegramRequestLog::where('telegramId', $chatId)->delete();
          $logArr = [
            "telegramId" => $chatId,
            "data" => '',
            'json_data' => json_decode($telegramRequestLog->json_data, true),
          ];
          if ($type == 'delete') {
            $logArr["command"] = $telegramRequestLog->command . "_showWeekDelete";
          } else {
            $logArr["command"] = $telegramRequestLog->command . "_showWeek";
          }
          $logArr['json_data']=json_encode(array_merge($logArr['json_data']));
          $calendar = $dateService->get_calendar((int)date('m'), (int)date('Y'));
          TelegramRequestLog::create($logArr);
          return $calendar;
    }

    public function validateCalendar(Request $request) {
      $dateService = app(DateService::class);
      $callbackRoute = explode('-', $request['data']);
      $chatId = $request['chat_id'];
      $messageId = $request['message_id'];
    if ($callbackRoute[0] === 'calendar' && $callbackRoute[1] === 'month') {
        $calendar = $dateService->get_calendar((int)$callbackRoute[2], (int)$callbackRoute[3]);
        return json_encode(['previousOrNextMonthText'=>true,
                            'previousOrNextMonthKeyboard'=> (array)$calendar]);
    } elseif ($callbackRoute[0] === 'calendar' && $callbackRoute[1] === 'year') {
        $months = $dateService->get_months_list((int)$callbackRoute[2]);
    } elseif($callbackRoute[0] === 'calendar' && $callbackRoute[1] === 'months_list') {
        $months = $dateService->get_months_list((int)$callbackRoute[2]);
    } elseif($callbackRoute[0] === 'calendar' && $callbackRoute[1] === 'years_list') {
        $months = $dateService->get_years_list((int)$callbackRoute[2]);
    } else {
        $telegramRequestLog = TelegramRequestLog::where('telegramId', $chatId)->first();
        if ($telegramRequestLog->command == 'booking:bookOrDelete_showWeekDelete') {
          // self::showTimeForDelete(
          //   $callbackRoute[2].'.'.$callbackRoute[3].'.'.$callbackRoute[4],
          // );
// ---------------------------Begin showTimeForDelete--------------------------------------
          $messageText = $callbackRoute[2].'.'.$callbackRoute[3].'.'.$callbackRoute[4];
          // Формат времени правильный?
          // 1. Нет
          $date = new \DateTime($messageText);
          if (!$dateService->validateDate($messageText)) {
            TelegramRequestLog::where('telegramId', $chatId)->delete();
            $arr = [
                'telegramId' => $chatId
            ];
            TelegramRequestLog::create($arr);
            return json_encode([
                  'validateDateText' => "Введенная дата не подходит формату ДД.ММ.ГГГГ попробуйте заново"
            ]);
          }

          $telegramRequestLog = TelegramRequestLog::where('telegramId', $chatId)->first();
          TelegramRequestLog::where('telegramId', $chatId)->delete();
          $logArr = [
            "telegramId" => $chatId,
            'json_data' => json_decode($telegramRequestLog->json_data, true),
          ];
          $room = $logArr['json_data']['room'];
          $logArr['json_data']=json_encode(array_merge($logArr['json_data'], ["date" => $messageText]));

          // 2. Да
          $buttons = [];
            $res = "У вас нет брони на выбранную дату. Выберите другую дату или нажмите «На главную», чтобы вернуться в главное меню";
            $records = BookRecord::where('date', $date->format('Y-m-d'))
              ->where('room', $room)
              ->where('telegramId', $chatId)
              ->orderBy('start','asc')
              ->get();
            if (count($records)) {
              $res = 'Выберите бронь, которую хотите удалить';
              for ($i = 0; $i < count($records); $i++) {
                array_push($buttons, "\n" . $dateService->formatDate($records[$i]->start, 'H:i') . "-" . $dateService->formatDate($records[$i]->end, 'H:i'));
              }
              $logArr["command"] = $telegramRequestLog->command . "_showTimeForDelete";
            } else {
              $logArr["command"] = $telegramRequestLog->command;
            }

          TelegramRequestLog::create($logArr);

          return json_encode([
              'result' => $res,
              'buttons' => $buttons
          ]);
//----------------------------End showTimeForDelete----------------------------------------



        } else {
            $messageText = $callbackRoute[2].'.'.$callbackRoute[3].'.'.$callbackRoute[4];
            $dateSelected = 'Выбрана дата '.$messageText;
            $calendar = $dateService->get_calendar((int)date('m'), (int)date('Y'));
            $reply_markup_for_catendar = (array)$calendar;
              if (!$dateService->isNotEndedDate($messageText)) {
                $text = "Вы не можете выбрать дату, которая уже прошла! Попробуйте заново";
                return json_encode(['isNotEndedDateText' => $text,
                                    'isNotEndedDateKeyboard' => $reply_markup_for_catendar,
                                    'dataSelected'=>$dateSelected]);
                }
              $date = new \DateTime($messageText);
            if (!$dateService->validateDate($messageText)) {
              return json_encode(['validateDateText' =>"Неверный формат даты. Попробуйте заново",
                                  'validateDateKeyboard' => $reply_markup_for_catendar,
                                  'dataSelected'=>$dateSelected]);
            }
            $telegramRequestLog = TelegramRequestLog::where('telegramId', $chatId)->first();
            TelegramRequestLog::where('telegramId', $chatId)->delete();
            $existingLogs = explode("_", $telegramRequestLog->command);
            if ($existingLogs[count($existingLogs) - 1] != 'showTime') {
              $telegramRequestLog->command .= "_showTime";
            }
            $logArr = [
              "command" => $telegramRequestLog->command,
              "telegramId" => $chatId,
              'json_data' => json_decode($telegramRequestLog->json_data, true),
            ];
            $room = $logArr['json_data']['room'];
            $logArr['json_data']=json_encode(array_merge($logArr['json_data'], ["date" => $messageText]));
            // 2. Да
            $res = "Комната переговоров свободна";
            $records = [];

              $records = BookRecord::where('date', $date->format('Y-m-d'))
                ->where('room', $room)
                ->orderBy('start','asc')
                ->get();
              // Если брони нет
              if (count($records)) {
                $res = "Это время уже занято твоими коллегами:";
                foreach ($records as $record) {
                  $res .= "\n" . $dateService->formatDate($record->start, 'H:i') . "-" . $dateService->formatDate($record->end, 'H:i') . ", " . $record->fio;
                }
              }

            $currentTime = "08:00";
            if ($dateService->isToday($messageText)) {
              $today = new \DateTime("now", new \DateTimeZone('Asia/Almaty'));
              $currentTime = $today->format("H:i");
            }
            $time = explode(":",$currentTime);
            $minutes = $time[1];
            $hour = $time[0];
            while ($minutes % 10) {
              if ($minutes > 50){
                $minutes = 0;
                $hour += 1;
              } else {
                $minutes += 1;
              }
            }
            $timePicker = $dateService->getTimePicker($hour, $minutes);
            $reply_markup = (array)$timePicker;

            TelegramRequestLog::create($logArr);
            $text = "Выберите время начала бронирования\n(Доступное время только с ".$currentTime." до 19:50)";
            return json_encode(['dataSelected'=>$dateSelected, 'result' => $res, 'text' => $text, 'reply_markup'=> $reply_markup]);
        }
    }
  }

    public function validateTimePicker(Request $request) {
      $dateService = app(DateService::class);
      $callbackRoute = explode('-', $request['data']);
      $chatId = $request['chat_id'];
      $firstname = $request['first_name'];
      $lastname = $request['last_name'];

      if ($callbackRoute[1] == 'all') {
        $telegramRequestLog = TelegramRequestLog::where('telegramId', $chatId)->first();
        if ($telegramRequestLog && $telegramRequestLog->command === 'booking:bookOrDelete_showWeek_showTime') {
// --------------------------------Begin Start Time--------------------------------------------
              $messageText = $callbackRoute[2].':'.$callbackRoute[3];
              $telegramRequestLog = TelegramRequestLog::where('telegramId', $chatId)->first();
              $logArr = [
                "command" => $telegramRequestLog->command . "_chooseStartTime",
                "telegramId" => $chatId,
                'json_data' => json_decode($telegramRequestLog->json_data, true),
              ];
              $room = $logArr['json_data']['room'];
              $date = $logArr['json_data']['date'];

              $currentTime = "08:00";
              if ($dateService->isToday($date)) {
                $today = new \DateTime("now", new \DateTimeZone('Asia/Almaty'));
                $minutes = (int)$today->format('i');
                $hour = (int)$today->format('H');
                while ($minutes % 10) {
                  if ($minutes > 50){
                    $minutes = 0;
                    $hour += 1;
                  } else {
                    $minutes += 1;
                  }
                }
                $today->setTime($hour, $minutes);
                $currentTime = $today->format("H:i");
              }

              $time = explode(":", $currentTime);
              $minutes = $time[1];
              $hour = $time[0];

              $timePicker = $dateService->getTimePicker($hour, $minutes);
              $reply_markup = (array)$timePicker;
              // Формат времени правильный?
              // 1. Нет
              $inputTimeArray = explode(":", $messageText);
              if (isset($inputTimeArray[1])) {
                if ((int)$inputTimeArray[1] < 10) {
                  $inputTimeArray[1] = "0".$inputTimeArray[1];
                  $messageText = $inputTimeArray[0].":".$inputTimeArray[1];
                }
              }
              $isNotValidatedRegex = !$dateService->validateTimeRegex($messageText);
              if ($isNotValidatedRegex) {
                return json_encode([
                      'isNotValidatedRegexText' => "Неверно указан формат времени\n(Доступное время только с ".$hour.":".$minutes." до 19:50)\nПопробуйте заново",
                      'isNotValidatedRegexKeyboard' => $reply_markup
                ]);
              }

              $isValidated = $dateService->validateTime($messageText, '07:59','20:00', $date);
              if (!$isValidated) {
                $currentTime = "08:00";
                if ($dateService->isToday($date)) {
                  $today = new \DateTime("now", new \DateTimeZone('Asia/Almaty'));
                  $minutes = (int)$today->format('i');
                  $hour = (int)$today->format('H');
                  while ($minutes % 10) {
                    if ($minutes > 50){
                      $minutes = 0;
                      $hour += 1;
                    } else {
                      $minutes += 1;
                    }
                  }
                  $today->setTime($hour, $minutes);
                  $currentTime = $today->format("H:i");
                }

                $time = explode(":", $currentTime);
                $timePicker = $dateService->getTimePicker((int)$time[0], (int)$time[1]);
                $reply_markup = (array)$timePicker;
                return json_encode([
                      'theTimeIsIncorrectText' => "Неверно указано время\n(Доступное время только с ".$currentTime." до 19:50)\nПопробуйте заново",
                      'theTimeIsIncorrectKeyboard' => $reply_markup
                ]);
              }

              $logArr['json_data']=json_encode(array_merge($logArr['json_data'], ["start" => $messageText]));

              $isBusy = false;
              $d = new \DateTime($date);
              $records = BookRecord::where('date', $d->format('Y-m-d'))->where('room', $room)->get();

              if (count($records)) {
                $customStart = strtotime($messageText);
                foreach ($records as $record) {
                  $start = strtotime($record->start);
                  $end = strtotime($record->end);
                  if ($customStart >= $start && $customStart <= $end) {
                    $isBusy = true;
                  }
                }
              }

              if ($isBusy) {
                return json_encode([
                      'timeIsBusyText' => "Данное время занято. Попробуйте заново",
                      'timeIsBusyKeyboard' => $reply_markup
                ]);
              } else {
                TelegramRequestLog::where('telegramId', $chatId)->delete();
              }
              // 2. Да
              $dateTime = new \DateTime($messageText);
              $dateTime = $dateTime->modify('+1 minutes');

              $minutes = (int)$dateTime->format('i');
              $hour = (int)$dateTime->format('H');
              while ($minutes % 10) {
                if ($minutes > 50){
                  $hour += 1;
                  $minutes = 0;
                } else {
                  $minutes += 1;
                }
              }
              $dateTime->setTime($hour, $minutes);

              $time = explode(":", $dateTime->format('H:i'));
              $timePicker = $dateService->getTimePicker((int)$time[0], (int)$time[1]);
              $reply_markup = (array)$timePicker;
              $res = "Введите время окончания бронирования в формате ЧЧ:ММ\n(Доступное время только с ".$dateTime->format("H:i")." до
              20:00)";
              TelegramRequestLog::create($logArr);
              return json_encode([
                  'resultText' => $res,
                  'resultKeyboard' => $reply_markup
              ]);
// --------------------------------End Start Time-----------------------------------------------------
        } else if ($telegramRequestLog && $telegramRequestLog->command === 'booking:bookOrDelete_showWeek_showTime_chooseStartTime') {
              $messageText = $callbackRoute[2].':'.$callbackRoute[3];
              $minutes = (int)date('i');
              $hour = (int)date('H');
              while ($minutes % 10) {
                if ($minutes > 50){
                  $minutes = 0;
                  $hour += 1;
                } else {
                  $minutes += 1;
                }
              }

              $timePicker = $dateService->getTimePicker($hour, (int)$minutes);
              $reply_markup = (array)$timePicker;

              // Формат времени правильный?
              // 1. Нет

              $telegramRequestLog = TelegramRequestLog::where('telegramId', $chatId)->first();
              $logArr = [
                "command" => $telegramRequestLog->command . "_chooseEndTime",
                "telegramId" => $chatId,
                'json_data' => json_decode($telegramRequestLog->json_data, true),
              ];
              $room = $logArr['json_data']['room'];
              $date = $logArr['json_data']['date'];
              $start = $logArr['json_data']['start'];

              $inputTimeArray = explode(":", $messageText);
              if (isset($inputTimeArray[1])) {
                if ((int)$inputTimeArray[1] < 10) {
                  $inputTimeArray[1] = "0".$inputTimeArray[1];
                  $messageText = $inputTimeArray[0].":".$inputTimeArray[1];
                }
              }
              $isNotValidatedRegex = !$dateService->validateTimeRegex($messageText);
              if ($isNotValidatedRegex) {
                return json_encode([
                     'isNotValidatedRegexText' => "Неверно указан формат времени\n(Доступное время только с ".$hour.":".$minutes." до 20:00)\nПопробуйте заново",
                     'isNotValidatedRegexKeyboard' => $reply_markup
                ]);
              }

              $isNotValidated = !$dateService->validateTime($messageText, $start, '20:01', $date);
              if ($isNotValidated) {
                $dateTime = new \DateTime($start);
                $dateTime = $dateTime->modify('+1 minutes');

                $minutes = (int)$dateTime->format('i');
                $hour = (int)$dateTime->format('H');
                while ($minutes % 10) {
                  if ($minutes > 50){
                    $minutes = 0;
                    $hour += 1;
                  } else {
                    $minutes += 1;
                  }
                }

                $timePicker = $dateService->getTimePicker((int)$hour, (int)$minutes);
                $reply_markup = (array)$timePicker;
                return json_encode([
                        'isNotValidatedText' => "Неверно указано время\n(Доступное время только с ".$dateTime->format("H:i")." до 20:00)\nПопробуйте заново",
                        'isNotValidatedKeyboard' => $reply_markup
                  ]);
                }
              $d = new \DateTime($logArr['json_data']['date']);
                  $isBusy = false;
                  $d = new \DateTime($date);
                  $records = BookRecord::where('date', $d->format('Y-m-d'))->where('room', $room)->get();
                  if (count($records)) {
                    $customStart = strtotime($messageText);
                    foreach ($records as $record) {
                      $start = strtotime($record->start);
                      $end = strtotime($record->end);
                      if ($customStart >= $start && $customStart <= $end) {
                        $isBusy = true;
                      }
                    }
                  }

                  if ($isBusy) {
                    $minutes = (int)date('i');
                    $hour = (int)date('H');
                    while ($minutes % 10) {
                      if ($minutes > 50){
                        $minutes = 0;
                        $hour += 1;
                      } else {
                        $minutes += 1;
                      }
                    }
                    $timePicker = $dateService->getTimePicker($hour, $minutes);

                    $reply_markup = (array)$timePicker;
                    return json_encode([
                        'isBusyText' => "Данное время занято. Попробуйте заново",
                        'isBusyKeyboard' => $reply_markup
                    ]);
                  }
                  $newRecord = new BookRecord();
                  $newRecord->start = $logArr['json_data']['start'];
                  $newRecord->end = $messageText;
                  $newRecord->date = $d->format('Y-m-d');
                  $newRecord->room = $logArr['json_data']['room'];
                  $newRecord->telegramId = $chatId;
                  $newRecord->fio = $firstname.' '.$lastname;
                  $newRecord->save();
              TelegramRequestLog::where('telegramId', $chatId)->delete();
              // 2. Да
              $logArr = [
                  'telegramId' => $chatId,
              ];

              TelegramRequestLog::create($logArr);

              $res = "Спасибо, ваше бронирование сохранено";
              return json_encode([
                  'result' => $res
              ]);
      }
    }else{
        $timepicker = $dateService->getTimePicker((int)$callbackRoute[2], (int)$callbackRoute[3]);
        return json_encode([
              'previousOrNextTimeText' => true,
              'previousOrNextTimeKeyboard' => $timepicker
        ]);
    }
  }

  public function confirmDeleteBooking(Request $request){
      $messageText = $request['data'];
      $chatId = $request['chat_id'];
        $telegramRequestLog = TelegramRequestLog::where('telegramId', $chatId)->first();
        TelegramRequestLog::where('telegramId', $chatId)->delete();
        $logArr = [
          "command" => $telegramRequestLog->command,
          "telegramId" => $chatId,
          'json_data' => json_decode($telegramRequestLog->json_data, true),
        ];
        $room = $logArr['json_data']['room'];
        $dates = explode("-", $messageText);

        // 2. Да
        $res = "У вас нет брони на выбранное время. Выберите другое время или нажмите «На главную», чтобы вернуться в главное меню";
        $buttons = false;
        $date = new \DateTime($logArr['json_data']['date']);
        if (count($dates) === 2) {
          $start = $dates[0];
          $end = $dates[1];
          $record = BookRecord::where('date', $date->format('Y-m-d'))
              ->where('room', $room)
              ->where('start', $start)
              ->where('end', $end)
              ->where('telegramId', $chatId)
              ->first();
          if ($record) {
              $res = "Вы уверены, что хотите удалить броню?";
              $buttons = true;
              $logArr["command"] = $telegramRequestLog->command . "_confirmDeleteBooking";
          }
        }
        $logArr['json_data']=json_encode(array_merge($logArr['json_data'], ["start" => $start, "end" => $end]));
        TelegramRequestLog::create($logArr);
        return json_encode([
              'result' => $res,
              'buttons' => $buttons
        ]);
  }

  public function deleteBooking(Request $request){
        $chatId = $request['chatId'];

        $telegramRequestLog = TelegramRequestLog::where('telegramId', $chatId)->first();
        TelegramRequestLog::where('telegramId', $chatId)->delete();
        $logArr = [
          "command" => $telegramRequestLog->command,
          "telegramId" => $chatId,
          'json_data' => json_decode($telegramRequestLog->json_data, true),
        ];
        $date = new \DateTime($logArr['json_data']['date']);
        $room = $logArr['json_data']['room'];
        $record = BookRecord::where('date', $date->format('Y-m-d'))
            ->where('room', $room)
            ->where('start', $logArr['json_data']['start'])
            ->where('end', $logArr['json_data']['end'])
            ->where('telegramId', $chatId)
            ->first();
        if ($record) {
          $record->delete();
        }

        TelegramRequestLog::create([
            'telegramId' => $chatId
        ]);

        $res = "Ваша броня удалена";
        return json_encode([
              'result' => $res
        ]);
  }
}
