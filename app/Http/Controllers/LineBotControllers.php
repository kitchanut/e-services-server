<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LineBotControllers extends Controller
{
    public function fireRequest()
    {
        $output = [];
        $output['type'] = 'text';
        $output['text'] = 'fire';
        return $output;
    }

    public function index(Request $request)
    {
        $data = file_get_contents('php://input');
        $deCode = json_decode($data, true);

        $replyToken = $deCode['events'][0]['replyToken'];
        $userId = $deCode['events'][0]['source']['userId'];
        $text = $deCode['events'][0]['message']['text'];

        $credentials['userId'] = $userId;
        $credentials['text'] = $text;
        $credentials['replyToken'] = $replyToken;
        $credentials['message'] = json_encode($deCode);

        // if ($text == 'GroupID') {
        $messages = [];
        $messages['replyToken'] = $replyToken;
        // $messages['messages'][0] = $this->getFormatTextMessage($text);
        $messages['messages'][0] = $this->fireRequest();

        $encodeJson = json_encode($messages);

        $LINEDatas['url'] = "https://api.line.me/v2/bot/message/reply";
        $LINEDatas['token'] = env("LINE_ACCESS_TOKEN");

        $results = $this->sentMessage($encodeJson, $LINEDatas);
        return response()->json($results);
        // }
    }
}
