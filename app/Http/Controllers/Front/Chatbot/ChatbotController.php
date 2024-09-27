<?php

namespace App\Http\Controllers\Front\Chatbot;

use App\Http\Controllers\Front\FrontController;
use Illuminate\Http\Request;

class ChatbotController extends FrontController
{
    public function index(Request $request)
    {
    }

    public function fullscreen(Request $request)
    {
        return view('front.' . config('bookdose.theme_front') . '.modules.chatbot.fullscreen');
    }
}
