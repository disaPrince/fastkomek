<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\News;
use App\Models\NewsReactions;
use App\Models\Clients;
use App\Models\ResultOfReaction;
use App\Exports\ReactionsExport;
use Maatwebsite\Excel\Excel;

class ExportController extends Controller
{
    public function getReactionsView()
    {
        return view('export.reactions');
    }

    public function getReactionsForExport(Request $request)
    {
        $data = News::select('id', 'content', 'title', 'updated_at')->whereBetween('updated_at', [Carbon::parse($request->dateStart)->startOfDay()->toDateTimeString(), Carbon::parse($request->dateEnd)->endOfDay()->toDateTimeString()])->get();
        return $data;
    }

    public function exportReaction(Request $request)
    {
        $reaction = NewsReactions::where('news_id', $request->id)->get();
        $users = [];
        foreach ($reaction as $item) {
            array_push($users, $item->telegramId);
        }
        $users = array_unique($users);
        foreach ($users as $user) {
            $telegramUser = Clients::where('telegramId', $user)->first();
            $name = new ResultOfReaction();
            $name->news_reactions_id = $request->id;
            $name->name = $telegramUser->name ?? null;
            $data = NewsReactions::where('telegramId', $user)->get();
            $resultEmoji = [];
            foreach ($data as $dat) {
                if ($dat->news_id == $request->id) {
                    switch ($dat->reaction) {
                        case '👍':
                            $name->good = "✅";
                            array_push($resultEmoji, '👍');
                            break;
                        case "👎":
                            $name->bad = "✅";
                            array_push($resultEmoji, '👎');
                            break;
                        case "🤔":
                            $name->whatever = "✅";
                            array_push($resultEmoji, "🤔");
                            break;
                    }
                }
            }
            if (!in_array('👍', $resultEmoji)) {
                $name->good = $name->good = "❌";
            }
            if (!in_array("🤔", $resultEmoji)) {
                $name->whatever = "❌";
            }
            if (!in_array('👎', $resultEmoji)) {
                $name->bad = "❌";
            }

            $name->save();
        }
        $contentName = News::select('content', 'title')->where('id', $request->id)->first();
        if ($contentName['title'] == null) {
            $result = (new ReactionsExport($request->id))->download(mb_substr($contentName['content'], 0, 20, 'UTF-8') . '.xlsx');
        } else {
            $result = (new ReactionsExport($request->id))->download(mb_substr($contentName['title'], 0, 20, 'UTF-8') . '.xlsx');
        }
        ResultOfReaction::where('news_reactions_id', $request->id)->delete();
        return $result;
    }
}