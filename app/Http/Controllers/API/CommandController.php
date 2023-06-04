<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Output\BufferedOutput;

class CommandController extends Controller
{
    public function command(Request $request)
    {

        Log::debug($request->get('key'));
        Log::debug(env('KEY'));
        if ($request->get('key') != env('KEY')) {
            return abort(403);
        }

        $output = new BufferedOutput();

        switch ($request->command) {
            case 'db:seed':
                Artisan::call('db:seed', ['--force' => true]);
                return $output->fetch();
            case 'migrate':
                Artisan::call('migrate', ['--force' => true], $output);
                return $output->fetch();
        }
    }
}