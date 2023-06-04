<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\Clients;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\FileUpload\InputFile;
use App\Models\News;
use App\Models\NewsImage;
use Telegram\Bot\Keyboard\Keyboard;
use App\Models\BotanStaff;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome', ['users' => Clients::all()]);
    }


    public function sendNotifications(Request $request)
    {
        $fileNames = [];
        $telegramUsers = [];

        if (!file_exists(storage_path('app/public/pictures'))) {
            File::makeDirectory(storage_path('app/public/pictures'), 0777, true);
        }
        $content = preg_replace("/\r|\n/", "", $request->message);
        $news = new News();
        $news->content = $content;
        $news->title = $request->title;
        $news->save();

        if ($request->hasFile('photoUpload')) {
            $files = $request->file('photoUpload');
            foreach ($files as $file) {
                $originFileName = substr($file->getClientOriginalName(), 0, strlen($file->getClientOriginalName()) - strlen($file->getClientOriginalExtension()) - 1);
                $fileName =
                    $originFileName . '.'
                    . $file->getClientOriginalExtension();
                $file->move(storage_path() . '/app/public/pictures/', $fileName);

                $newsFile = new NewsImage();
                $newsFile->news_id = $news->id;
                $newsFile->path = '/pictures/' . $fileName;
                $newsFile->save();
                array_push($fileNames, $fileName);
            }
        }

        if ($request->company === "user") {
            $users = Clients::whereIn('telegramId', $request->users)->get();
            foreach ($users as $user) {
                array_push($telegramUsers, $user);
            }
        } else {
            $users = Clients::all();
            foreach ($users as $user) {
                array_push($telegramUsers, $user);
            }
        }

        $reply_markup = Keyboard::make([
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
            'inline_keyboard' => [
                [
                    ['text' => '👍', 'callback_data' => '👍#poll' . $news->id],
                    ['text' => '🤔', 'callback_data' => '🤔#poll' . $news->id],
                    ['text' => '👎', 'callback_data' => '👎#poll' . $news->id],
                ]
            ]
        ]);

        foreach ($telegramUsers as $telegramUser) {
            if ($request->message) {
                if ($fileNames) {
                    foreach ($fileNames as $fileName) {
                        if (strpos($fileName, 'xlsx') || strpos($fileName, 'lsx') || strpos($fileName, 'docx') || strpos($fileName, 'doc' || strpos($fileName, 'pdf'))) {
                            Telegram::sendDocument([
                                'chat_id' => $telegramUser->telegramId,
                                'document' => new InputFile(storage_path() . '/app/public/pictures/' . $fileName, $fileName),
                            ]);
                        } else {
                            Telegram::sendPhoto([
                                'chat_id' => $telegramUser->telegramId,
                                'photo' => new InputFile(storage_path() . '/app/public/pictures/' . $fileName, $fileName),
                            ]);
                        }
                    }
                }

                Telegram::sendMessage([
                    'chat_id' => $telegramUser->telegramId,
                    'text' => $request->message,
                    'reply_markup' => $reply_markup
                ]);

            } else {
                if ($fileNames) {
                    foreach ($fileNames as $key => $fileName) {
                        if ($key == (count($fileNames) - 1)) {
                            if (strpos($fileName, 'xlsx') || strpos($fileName, 'lsx') || strpos($fileName, 'docx') || strpos($fileName, 'doc' || strpos($fileName, 'pdf'))) {
                                Telegram::sendDocument([
                                    'chat_id' => $telegramUser->telegramId,
                                    'document' => new InputFile(storage_path() . '/app/public/pictures/' . $fileName, $fileName),
                                    'reply_markup' => $reply_markup
                                ]);
                            } else {
                                Telegram::sendPhoto([
                                    'chat_id' => $telegramUser->telegramId,
                                    'photo' => new InputFile(storage_path() . '/app/public/pictures/' . $fileName, $fileName),
                                    'reply_markup' => $reply_markup
                                ]);
                            }
                        } else {
                            if (strpos($fileName, 'xlsx') || strpos($fileName, 'lsx') || strpos($fileName, 'docx') || strpos($fileName, 'doc' || strpos($fileName, 'pdf'))) {
                                Telegram::sendDocument([
                                    'chat_id' => $telegramUser->telegramId,
                                    'document' => new InputFile(storage_path() . '/app/public/pictures/' . $fileName, $fileName),
                                ]);
                            } else {
                                Telegram::sendPhoto([
                                    'chat_id' => $telegramUser->telegramId,
                                    'photo' => new InputFile(storage_path() . '/app/public/pictures/' . $fileName, $fileName),
                                ]);
                            }
                        }
                    }
                }
            }
        }
        return redirect()->route('index');
    }

    public function showStaff()
    {
        return view('staff', ['clients' => Clients::all()]);
    }
}