<?php

namespace App\Http\Controllers;

use App\Linebot;
use Illuminate\Http\Request;

class LinebotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = file_get_contents('php://input');
        $deCode = json_decode($data, true);

        $replyToken = $deCode['events'][0]['replyToken'];
        $userId = $deCode['events'][0]['source']['userId'];
        if ($deCode['events'][0]['source']['type'] == 'group') {
            $groupId = $deCode['events'][0]['source']['groupId'];
        } else {
            $groupId = '';
        }
        $text = $deCode['events'][0]['message']['text'];

        $credentials['userId'] = $userId;
        $credentials['groupId'] = $groupId;
        $credentials['text'] = $text;
        $credentials['replyToken'] = $replyToken;
        $credentials['message'] = json_encode($deCode);

        if ($text === 'แจ้งเหตุ') {
            $jayParsedAry = [
                "type" => "flex",
                "altText" => "เลือกประเภทที่แจ้งเหตุ",
                "contents" => [
                    "type" => "bubble",
                    "header" => [
                        "type" => "box",
                        "layout" => "vertical",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "vertical",
                                "contents" => [
                                    [
                                        "type" => "box",
                                        "layout" => "vertical",
                                        "contents" => [
                                            [
                                                "type" => "box",
                                                "layout" => "vertical",
                                                "contents" => [
                                                    [
                                                        "type" => "text",
                                                        "contents" => [],
                                                        "size" => "xl",
                                                        "wrap" => true,
                                                        "text" => "เลือกประเภทที่แจ้งเหตุ",
                                                        "color" => "#ffffff",
                                                        "weight" => "bold"
                                                    ]
                                                ],
                                                "spacing" => "sm",
                                                "justifyContent" => "center",
                                                "alignItems" => "center"
                                            ]
                                        ]
                                    ]
                                ],
                                "paddingAll" => "20px",
                                "backgroundColor" => "#F44336"
                            ]
                        ],
                        "paddingAll" => "0px"
                    ],
                    "footer" => [
                        "type" => "box",
                        "layout" => "vertical",
                        "contents" => [
                            [
                                "type" => "button",
                                "action" => [
                                    "type" => "uri",
                                    "label" => "เพลิงไหม้",
                                    "uri" => "https://liff.line.me/1660802157-GlyPbLOR?type=fire"
                                ],
                                "style" => "secondary",
                                "adjustMode" => "shrink-to-fit"
                            ],
                            [
                                "type" => "button",
                                "action" => [
                                    "type" => "uri",
                                    "label" => "ไฟส่องสว่าง",
                                    "uri" => "https://liff.line.me/1660802157-GlyPbLOR?type=pole"
                                ],
                                "style" => "secondary"
                            ],
                            [
                                "type" => "button",
                                "action" => [
                                    "type" => "uri",
                                    "label" => "ประปาชำรุด",
                                    "uri" => "https://liff.line.me/1660802157-GlyPbLOR?type=water"
                                ],
                                "style" => "secondary"
                            ],
                            [
                                "type" => "button",
                                "action" => [
                                    "type" => "uri",
                                    "label" => "งานทางชำรุด",
                                    "uri" => "https://liff.line.me/1660802157-GlyPbLOR?type=road"
                                ],
                                "style" => "secondary"
                            ],
                            [
                                "type" => "button",
                                "action" => [
                                    "type" => "uri",
                                    "label" => "ฉุกเฉิน (EMS)",
                                    "uri" => "https://liff.line.me/1660802157-GlyPbLOR?type=ems"
                                ],
                                "style" => "secondary"
                            ],
                            [
                                "type" => "separator",
                                "margin" => "md"
                            ],
                            [
                                "type" => "box",
                                "layout" => "vertical",
                                "contents" => [
                                    [
                                        "type" => "button",
                                        "action" => [
                                            "type" => "uri",
                                            "label" => "ทุจริตและประพฤติมิชอบ",
                                            "uri" => "https://liff.line.me/1660802157-Qpv3V0Oy"
                                        ],
                                        "style" => "secondary",
                                        "adjustMode" => "shrink-to-fit",
                                        "color" => "#FFC107"
                                    ],
                                    [
                                        "type" => "button",
                                        "action" => [
                                            "type" => "uri",
                                            "label" => "เบาะแสยาเสพติด",
                                            "uri" => "https://liff.line.me/1660802157-ZxYVGKp5"
                                        ],
                                        "style" => "secondary",
                                        "adjustMode" => "shrink-to-fit",
                                        "color" => "#FFC107"
                                    ]
                                ],
                                "margin" => "md",
                                "spacing" => "sm"
                            ],
                            [
                                "type" => "separator",
                                "margin" => "md"
                            ],
                            [
                                "type" => "box",
                                "layout" => "vertical",
                                "contents" => [
                                    [
                                        "type" => "button",
                                        "action" => [
                                            "type" => "uri",
                                            "label" => "คำร้องขอข้อมูลข่าวสาร",
                                            "uri" => "https://liff.line.me/1660802157-EmQZ832O"
                                        ],
                                        "style" => "primary",
                                        "adjustMode" => "shrink-to-fit"
                                    ],
                                    [
                                        "type" => "button",
                                        "action" => [
                                            "type" => "uri",
                                            "label" => "เสนอแผนพัฒนาท้องถิ่น",
                                            "uri" => "https://liff.line.me/1660802157-gqR0eoMk"
                                        ],
                                        "style" => "primary",
                                        "adjustMode" => "shrink-to-fit"
                                    ]
                                ],
                                "margin" => "md",
                                "spacing" => "sm"
                            ]
                        ],
                        "spacing" => "sm"
                    ]
                ]
            ];

            $LINEDatas['url'] = "https://api.line.me/v2/bot/message/reply";
            $LINEDatas['token'] = env("LINE_ACCESS_TOKEN");

            $messages = [];
            $messages['replyToken'] = $replyToken;
            $messages['messages'][0] = $jayParsedAry;
            $encodeJson = json_encode($messages);
            $results = $this->sentMessage($encodeJson, $LINEDatas);
            return response()->json($results);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Linebot  $linebot
     * @return \Illuminate\Http\Response
     */
    public function show(Linebot $linebot)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Linebot  $linebot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Linebot $linebot)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Linebot  $linebot
     * @return \Illuminate\Http\Response
     */
    public function destroy(Linebot $linebot)
    {
        //
    }
}
