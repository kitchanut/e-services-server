<?php

namespace App\Http\Controllers;

use App\Prefix;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function getFormatTextMessage($text)
    {
        $datas = [];
        $datas['type'] = 'text';
        $datas['text'] = $text;
        return $datas;
    }

    public function sentMessage($encodeJson, $datas)
    {
        $datasReturn = [];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $datas['url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $encodeJson,
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $datas['token'],
                "cache-control: no-cache",
                "content-type: application/json; charset=UTF-8",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $datasReturn['result'] = 'E';
            $datasReturn['message'] = $err;
        } else {
            if ($response == "{}") {
                $datasReturn['result'] = 'S';
                $datasReturn['message'] = 'Success';
            } else {
                $datasReturn['result'] = 'E';
                $datasReturn['message'] = $response;
            }
        }
        return $datasReturn;
    }

    public function getPrefix($type_prefix)
    {
        // $type_prefix = "IN";
        $strNextSeq = "";
        $objQuery = Prefix::where('type', $type_prefix)->first();

        $val_q =  $objQuery->val;
        $seq_q = $objQuery->seq;

        if ($val_q == date("Y") . "-" . date("m")) {
            $Seq = substr("00000" . $seq_q, -5, 5);
            $strNextSeq = date("Y") . date("m") . $Seq;
            $data_prefix['seq'] = $Seq + 1;
            $objQuery->update($data_prefix);
        } else {
            $Seq = substr("000001", -5, 5);
            $strNextSeq = date("Y") . date("m") .  $Seq;
            $data_prefix['val'] = date("Y") . "-" . date("m");
            $data_prefix['seq'] = '2';
            $objQuery->update($data_prefix);
        }
        $number =  $type_prefix . $strNextSeq;
        return $number;
    }

    public function sentCardInform($LineUUID, $title, $number, $informDate, $informTime)
    {
        $jayParsedAry = [
            "type" => "flex",
            "altText" => $title . " : " . $number,
            "contents" => [
                "type" => "bubble",
                "size" => "mega",
                "header" => [
                    "type" => "box",
                    "layout" => "vertical",
                    "contents" => [
                        [
                            "type" => "box",
                            "layout" => "vertical",
                            "contents" => [
                                [
                                    "type" => "text",
                                    "text" => $title,
                                    "color" => "#ffffff",
                                    "size" => "xl",
                                    "flex" => 4,
                                    "weight" => "bold"
                                ],
                                [
                                    "type" => "text",
                                    "text" => $number,
                                    "color" => "#ffffff66"
                                ]
                            ]
                        ]
                    ],
                    "paddingAll" => "20px",
                    "backgroundColor" => "#FF6B6D",
                    "spacing" => "md",
                    "paddingTop" => "22px"
                ],
                "body" => [
                    "type" => "box",
                    "layout" => "vertical",
                    "contents" => [
                        [
                            "type" => "text",
                            "text" => "รอรับเรื่อง",
                            "color" => "#FF6B6D",
                            "size" => "xs"
                        ],
                        [
                            "type" => "box",
                            "layout" => "vertical",
                            "contents" => [
                                [
                                    "type" => "box",
                                    "layout" => "vertical",
                                    "contents" => [
                                        [
                                            "type" => "filler"
                                        ]
                                    ],
                                    "width" => "30%",
                                    "height" => "6px",
                                    "backgroundColor" => "#FF6B6D"
                                ]
                            ],
                            "height" => "6px",
                            "backgroundColor" => "#9FD8E36E"
                        ],
                        [
                            "type" => "box",
                            "layout" => "horizontal",
                            "contents" => [
                                [
                                    "type" => "box",
                                    "layout" => "vertical",
                                    "contents" => [
                                        [
                                            "type" => "text",
                                            "text" => $informDate,
                                            "size" => "sm",
                                            "gravity" => "center"
                                        ],
                                        [
                                            "type" => "text",
                                            "text" => $informTime
                                        ]
                                    ],
                                    "margin" => "none",
                                    "cornerRadius" => "none",
                                    "width" => "90px"
                                ],
                                [
                                    "type" => "box",
                                    "layout" => "vertical",
                                    "contents" => [
                                        [
                                            "type" => "filler"
                                        ],
                                        [
                                            "type" => "box",
                                            "layout" => "vertical",
                                            "contents" => [],
                                            "cornerRadius" => "30px",
                                            "height" => "12px",
                                            "width" => "12px",
                                            "borderColor" => "#EF454D",
                                            "borderWidth" => "2px"
                                        ],
                                        [
                                            "type" => "filler"
                                        ]
                                    ],
                                    "width" => "20px"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "แจ้งเรื่อง",
                                    "gravity" => "center",
                                    "flex" => 4,
                                    "size" => "sm"
                                ]
                            ],
                            "spacing" => "none",
                            "margin" => "lg"
                        ],
                        [
                            "type" => "box",
                            "layout" => "horizontal",
                            "contents" => [
                                [
                                    "type" => "box",
                                    "layout" => "baseline",
                                    "contents" => [
                                        [
                                            "type" => "filler"
                                        ]
                                    ],
                                    "width" => "95px"
                                ],
                                [
                                    "type" => "box",
                                    "layout" => "horizontal",
                                    "contents" => [
                                        [
                                            "type" => "box",
                                            "layout" => "horizontal",
                                            "contents" => [
                                                [
                                                    "type" => "box",
                                                    "layout" => "vertical",
                                                    "contents" => [],
                                                    "width" => "2px",
                                                    "backgroundColor" => "#B7B7B7"
                                                ],
                                                [
                                                    "type" => "filler"
                                                ]
                                            ],
                                            "flex" => 1
                                        ]
                                    ],
                                    "width" => "20px"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "รอเจ้าหน้าที่รับเรื่อง",
                                    "gravity" => "center",
                                    "flex" => 4,
                                    "size" => "xs",
                                    "color" => "#8c8c8c"
                                ]
                            ],
                            "spacing" => "none",
                            "height" => "60px"
                        ],
                    ]
                ],
                "footer" => [
                    "type" => "box",
                    "layout" => "vertical",
                    "contents" => [
                        [
                            "type" => "button",
                            "action" => [
                                "type" => "uri",
                                "label" => "รายละเอียด",
                                "uri" => "https://liff.line.me/1660802157-KM5m1bE9"
                            ],
                            "style" => "secondary"
                        ]
                    ]
                ]
            ]

        ];

        $LINEDatas['url'] = "https://api.line.me/v2/bot/message/push";
        $LINEDatas['token'] = env("LINE_ACCESS_TOKEN");

        $messages = [];
        $messages['to'] = $LineUUID;
        $messages['messages'][0] = $jayParsedAry;
        $encodeJson = json_encode($messages);
        $results = $this->sentMessage($encodeJson, $LINEDatas);
        return response()->json($results);
    }

    public function sentCardAccept($LineUUID, $title, $number, $informDate, $informTime, $acceptDate, $acceptTime)
    {
        $jayParsedAry = [
            "type" => "flex",
            "altText" => "เจ้าหน้าที่รับเรื่อง",
            "contents" => [
                "type" => "bubble",
                "size" => "mega",
                "header" => [
                    "type" => "box",
                    "layout" => "vertical",
                    "contents" => [
                        [
                            "type" => "box",
                            "layout" => "vertical",
                            "contents" => [
                                [
                                    "type" => "text",
                                    "text" => $title,
                                    "color" => "#ffffff",
                                    "size" => "xl",
                                    "flex" => 4,
                                    "weight" => "bold"
                                ],
                                [
                                    "type" => "text",
                                    "text" => $number,
                                    "color" => "#ffffff66"
                                ]
                            ]
                        ]
                    ],
                    "paddingAll" => "20px",
                    "backgroundColor" => "#0367D3",
                    "spacing" => "md",
                    "paddingTop" => "22px"
                ],
                "body" => [
                    "type" => "box",
                    "layout" => "vertical",
                    "contents" => [
                        [
                            "type" => "text",
                            "text" => "เจ้าหน้าที่รับเรื่องแล้ว",
                            "color" => "#0566D3",
                            "size" => "xs"
                        ],
                        [
                            "type" => "box",
                            "layout" => "vertical",
                            "contents" => [
                                [
                                    "type" => "box",
                                    "layout" => "vertical",
                                    "contents" => [
                                        [
                                            "type" => "filler"
                                        ]
                                    ],
                                    "width" => "70%",
                                    "height" => "6px",
                                    "backgroundColor" => "#0566D3"
                                ]
                            ],
                            "height" => "6px",
                            "backgroundColor" => "#9FD8E36E"
                        ],
                        [
                            "type" => "box",
                            "layout" => "horizontal",
                            "contents" => [
                                [
                                    "type" => "box",
                                    "layout" => "vertical",
                                    "contents" => [
                                        [
                                            "type" => "text",
                                            "text" => $informDate,
                                            "size" => "sm",
                                            "gravity" => "center"
                                        ],
                                        [
                                            "type" => "text",
                                            "text" => $informTime
                                        ]
                                    ],
                                    "margin" => "none",
                                    "cornerRadius" => "none",
                                    "width" => "90px"
                                ],
                                [
                                    "type" => "box",
                                    "layout" => "vertical",
                                    "contents" => [
                                        [
                                            "type" => "filler"
                                        ],
                                        [
                                            "type" => "box",
                                            "layout" => "vertical",
                                            "contents" => [],
                                            "cornerRadius" => "30px",
                                            "height" => "12px",
                                            "width" => "12px",
                                            "borderColor" => "#EF454D",
                                            "borderWidth" => "2px"
                                        ],
                                        [
                                            "type" => "filler"
                                        ]
                                    ],
                                    "width" => "20px"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "แจ้งเรื่อง",
                                    "gravity" => "center",
                                    "flex" => 4,
                                    "size" => "sm"
                                ]
                            ],
                            "spacing" => "none",
                            "margin" => "lg"
                        ],
                        [
                            "type" => "box",
                            "layout" => "horizontal",
                            "contents" => [
                                [
                                    "type" => "box",
                                    "layout" => "baseline",
                                    "contents" => [
                                        [
                                            "type" => "filler"
                                        ]
                                    ],
                                    "width" => "95px"
                                ],
                                [
                                    "type" => "box",
                                    "layout" => "horizontal",
                                    "contents" => [
                                        [
                                            "type" => "box",
                                            "layout" => "horizontal",
                                            "contents" => [
                                                [
                                                    "type" => "box",
                                                    "layout" => "vertical",
                                                    "contents" => [],
                                                    "width" => "2px",
                                                    "backgroundColor" => "#B7B7B7"
                                                ],
                                                [
                                                    "type" => "filler"
                                                ]
                                            ],
                                            "flex" => 1
                                        ]
                                    ],
                                    "width" => "20px"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "รอเจ้าหน้าที่รับเรื่อง",
                                    "gravity" => "center",
                                    "flex" => 4,
                                    "size" => "xs",
                                    "color" => "#8c8c8c"
                                ]
                            ],
                            "spacing" => "none",
                            "height" => "60px"
                        ],
                        [
                            "type" => "box",
                            "layout" => "horizontal",
                            "contents" => [
                                [
                                    "type" => "box",
                                    "layout" => "vertical",
                                    "contents" => [
                                        [
                                            "type" => "text",
                                            "text" => $acceptDate,
                                            "size" => "sm",
                                            "gravity" => "center"
                                        ],
                                        [
                                            "type" => "text",
                                            "text" => $acceptTime
                                        ]
                                    ],
                                    "margin" => "none",
                                    "cornerRadius" => "none",
                                    "width" => "90px"
                                ],
                                [
                                    "type" => "box",
                                    "layout" => "vertical",
                                    "contents" => [
                                        [
                                            "type" => "filler"
                                        ],
                                        [
                                            "type" => "box",
                                            "layout" => "vertical",
                                            "contents" => [],
                                            "cornerRadius" => "30px",
                                            "height" => "12px",
                                            "width" => "12px",
                                            "borderColor" => "#0566D3",
                                            "borderWidth" => "2px"
                                        ],
                                        [
                                            "type" => "filler"
                                        ]
                                    ],
                                    "width" => "20px"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "รับเรื่อง",
                                    "gravity" => "center",
                                    "flex" => 4,
                                    "size" => "sm"
                                ]
                            ],
                            "spacing" => "none",
                            "margin" => "lg"
                        ],
                        [
                            "type" => "box",
                            "layout" => "horizontal",
                            "contents" => [
                                [
                                    "type" => "box",
                                    "layout" => "baseline",
                                    "contents" => [
                                        [
                                            "type" => "filler"
                                        ]
                                    ],
                                    "width" => "95px"
                                ],
                                [
                                    "type" => "box",
                                    "layout" => "horizontal",
                                    "contents" => [
                                        [
                                            "type" => "box",
                                            "layout" => "horizontal",
                                            "contents" => [
                                                [
                                                    "type" => "box",
                                                    "layout" => "vertical",
                                                    "contents" => [],
                                                    "width" => "2px",
                                                    "backgroundColor" => "#B7B7B7"
                                                ],
                                                [
                                                    "type" => "filler"
                                                ]
                                            ],
                                            "flex" => 1
                                        ]
                                    ],
                                    "width" => "20px"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "กำลังดำเนินการ",
                                    "gravity" => "center",
                                    "flex" => 4,
                                    "size" => "xs",
                                    "color" => "#8c8c8c"
                                ]
                            ],
                            "spacing" => "none",
                            "height" => "60px"
                        ],
                        // [
                        //     "type" => "box",
                        //     "layout" => "horizontal",
                        //     "contents" => [
                        //         [
                        //             "type" => "box",
                        //             "layout" => "vertical",
                        //             "contents" => [
                        //                 [
                        //                     "type" => "text",
                        //                     "text" => "2023-04-22",
                        //                     "size" => "sm",
                        //                     "gravity" => "center"
                        //                 ],
                        //                 [
                        //                     "type" => "text",
                        //                     "text" => "20:20:00"
                        //                 ]
                        //             ],
                        //             "margin" => "none",
                        //             "cornerRadius" => "none",
                        //             "width" => "90px"
                        //         ],
                        //         [
                        //             "type" => "box",
                        //             "layout" => "vertical",
                        //             "contents" => [
                        //                 [
                        //                     "type" => "filler"
                        //                 ],
                        //                 [
                        //                     "type" => "box",
                        //                     "layout" => "vertical",
                        //                     "contents" => [],
                        //                     "cornerRadius" => "30px",
                        //                     "height" => "12px",
                        //                     "width" => "12px",
                        //                     "borderColor" => "#4CAF50",
                        //                     "borderWidth" => "2px"
                        //                 ],
                        //                 [
                        //                     "type" => "filler"
                        //                 ]
                        //             ],
                        //             "width" => "20px"
                        //         ],
                        //         [
                        //             "type" => "text",
                        //             "text" => "ดำเนินการเสร็จสิ้น",
                        //             "gravity" => "center",
                        //             "flex" => 4,
                        //             "size" => "sm"
                        //         ]
                        //     ],
                        //     "spacing" => "none",
                        //     "margin" => "lg"
                        // ]
                    ]
                ],
                "footer" => [
                    "type" => "box",
                    "layout" => "vertical",
                    "contents" => [
                        [
                            "type" => "button",
                            "action" => [
                                "type" => "uri",
                                "label" => "รายละเอียด",
                                "uri" => "https://liff.line.me/1660802157-KM5m1bE9"
                            ],
                            "style" => "secondary"
                        ]
                    ]
                ]
            ]

        ];

        $LINEDatas['url'] = "https://api.line.me/v2/bot/message/push";
        $LINEDatas['token'] = env("LINE_ACCESS_TOKEN");

        $messages = [];
        $messages['to'] = $LineUUID;
        $messages['messages'][0] = $jayParsedAry;
        $encodeJson = json_encode($messages);
        $results = $this->sentMessage($encodeJson, $LINEDatas);
        return response()->json($results);
    }

    public function sentCardClose($LineUUID, $title, $number, $informDate, $informTime, $acceptDate, $acceptTime, $closeDate, $closeTime)
    {
        $jayParsedAry = [
            "type" => "flex",
            "altText" => "ดำเนินการเสร็จสิ้น",
            "contents" => [
                "type" => "bubble",
                "size" => "mega",
                "header" => [
                    "type" => "box",
                    "layout" => "vertical",
                    "contents" => [
                        [
                            "type" => "box",
                            "layout" => "vertical",
                            "contents" => [
                                [
                                    "type" => "text",
                                    "text" => $title,
                                    "color" => "#ffffff",
                                    "size" => "xl",
                                    "flex" => 4,
                                    "weight" => "bold"
                                ],
                                [
                                    "type" => "text",
                                    "text" => $number,
                                    "color" => "#ffffff66"
                                ]
                            ]
                        ]
                    ],
                    "paddingAll" => "20px",
                    "backgroundColor" => "#4CAF50",
                    "spacing" => "md",
                    "paddingTop" => "22px"
                ],
                "body" => [
                    "type" => "box",
                    "layout" => "vertical",
                    "contents" => [
                        [
                            "type" => "text",
                            "text" => "ดำเนินเสร็จสิ้น",
                            "color" => "#4CAF50",
                            "size" => "xs"
                        ],
                        [
                            "type" => "box",
                            "layout" => "vertical",
                            "contents" => [
                                [
                                    "type" => "box",
                                    "layout" => "vertical",
                                    "contents" => [
                                        [
                                            "type" => "filler"
                                        ]
                                    ],
                                    "width" => "100%",
                                    "height" => "6px",
                                    "backgroundColor" => "#4CAF50"
                                ]
                            ],
                            "height" => "6px",
                            "backgroundColor" => "#9FD8E36E"
                        ],
                        [
                            "type" => "box",
                            "layout" => "horizontal",
                            "contents" => [
                                [
                                    "type" => "box",
                                    "layout" => "vertical",
                                    "contents" => [
                                        [
                                            "type" => "text",
                                            "text" => $informDate,
                                            "size" => "sm",
                                            "gravity" => "center"
                                        ],
                                        [
                                            "type" => "text",
                                            "text" => $informTime
                                        ]
                                    ],
                                    "margin" => "none",
                                    "cornerRadius" => "none",
                                    "width" => "90px"
                                ],
                                [
                                    "type" => "box",
                                    "layout" => "vertical",
                                    "contents" => [
                                        [
                                            "type" => "filler"
                                        ],
                                        [
                                            "type" => "box",
                                            "layout" => "vertical",
                                            "contents" => [],
                                            "cornerRadius" => "30px",
                                            "height" => "12px",
                                            "width" => "12px",
                                            "borderColor" => "#EF454D",
                                            "borderWidth" => "2px"
                                        ],
                                        [
                                            "type" => "filler"
                                        ]
                                    ],
                                    "width" => "20px"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "แจ้งเรื่อง",
                                    "gravity" => "center",
                                    "flex" => 4,
                                    "size" => "sm"
                                ]
                            ],
                            "spacing" => "none",
                            "margin" => "lg"
                        ],
                        [
                            "type" => "box",
                            "layout" => "horizontal",
                            "contents" => [
                                [
                                    "type" => "box",
                                    "layout" => "baseline",
                                    "contents" => [
                                        [
                                            "type" => "filler"
                                        ]
                                    ],
                                    "width" => "95px"
                                ],
                                [
                                    "type" => "box",
                                    "layout" => "horizontal",
                                    "contents" => [
                                        [
                                            "type" => "box",
                                            "layout" => "horizontal",
                                            "contents" => [
                                                [
                                                    "type" => "box",
                                                    "layout" => "vertical",
                                                    "contents" => [],
                                                    "width" => "2px",
                                                    "backgroundColor" => "#B7B7B7"
                                                ],
                                                [
                                                    "type" => "filler"
                                                ]
                                            ],
                                            "flex" => 1
                                        ]
                                    ],
                                    "width" => "20px"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "รอเจ้าหน้าที่รับเรื่อง",
                                    "gravity" => "center",
                                    "flex" => 4,
                                    "size" => "xs",
                                    "color" => "#8c8c8c"
                                ]
                            ],
                            "spacing" => "none",
                            "height" => "60px"
                        ],
                        [
                            "type" => "box",
                            "layout" => "horizontal",
                            "contents" => [
                                [
                                    "type" => "box",
                                    "layout" => "vertical",
                                    "contents" => [
                                        [
                                            "type" => "text",
                                            "text" => $acceptDate,
                                            "size" => "sm",
                                            "gravity" => "center"
                                        ],
                                        [
                                            "type" => "text",
                                            "text" => $acceptTime
                                        ]
                                    ],
                                    "margin" => "none",
                                    "cornerRadius" => "none",
                                    "width" => "90px"
                                ],
                                [
                                    "type" => "box",
                                    "layout" => "vertical",
                                    "contents" => [
                                        [
                                            "type" => "filler"
                                        ],
                                        [
                                            "type" => "box",
                                            "layout" => "vertical",
                                            "contents" => [],
                                            "cornerRadius" => "30px",
                                            "height" => "12px",
                                            "width" => "12px",
                                            "borderColor" => "#0566D3",
                                            "borderWidth" => "2px"
                                        ],
                                        [
                                            "type" => "filler"
                                        ]
                                    ],
                                    "width" => "20px"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "รับเรื่อง",
                                    "gravity" => "center",
                                    "flex" => 4,
                                    "size" => "sm"
                                ]
                            ],
                            "spacing" => "none",
                            "margin" => "lg"
                        ],
                        [
                            "type" => "box",
                            "layout" => "horizontal",
                            "contents" => [
                                [
                                    "type" => "box",
                                    "layout" => "baseline",
                                    "contents" => [
                                        [
                                            "type" => "filler"
                                        ]
                                    ],
                                    "width" => "95px"
                                ],
                                [
                                    "type" => "box",
                                    "layout" => "horizontal",
                                    "contents" => [
                                        [
                                            "type" => "box",
                                            "layout" => "horizontal",
                                            "contents" => [
                                                [
                                                    "type" => "box",
                                                    "layout" => "vertical",
                                                    "contents" => [],
                                                    "width" => "2px",
                                                    "backgroundColor" => "#B7B7B7"
                                                ],
                                                [
                                                    "type" => "filler"
                                                ]
                                            ],
                                            "flex" => 1
                                        ]
                                    ],
                                    "width" => "20px"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "กำลังดำเนินการ",
                                    "gravity" => "center",
                                    "flex" => 4,
                                    "size" => "xs",
                                    "color" => "#8c8c8c"
                                ]
                            ],
                            "spacing" => "none",
                            "height" => "60px"
                        ],
                        [
                            "type" => "box",
                            "layout" => "horizontal",
                            "contents" => [
                                [
                                    "type" => "box",
                                    "layout" => "vertical",
                                    "contents" => [
                                        [
                                            "type" => "text",
                                            "text" => $closeDate,
                                            "size" => "sm",
                                            "gravity" => "center"
                                        ],
                                        [
                                            "type" => "text",
                                            "text" => $closeTime
                                        ]
                                    ],
                                    "margin" => "none",
                                    "cornerRadius" => "none",
                                    "width" => "90px"
                                ],
                                [
                                    "type" => "box",
                                    "layout" => "vertical",
                                    "contents" => [
                                        [
                                            "type" => "filler"
                                        ],
                                        [
                                            "type" => "box",
                                            "layout" => "vertical",
                                            "contents" => [],
                                            "cornerRadius" => "30px",
                                            "height" => "12px",
                                            "width" => "12px",
                                            "borderColor" => "#4CAF50",
                                            "borderWidth" => "2px"
                                        ],
                                        [
                                            "type" => "filler"
                                        ]
                                    ],
                                    "width" => "20px"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "ดำเนินการเสร็จสิ้น",
                                    "gravity" => "center",
                                    "flex" => 4,
                                    "size" => "sm"
                                ]
                            ],
                            "spacing" => "none",
                            "margin" => "lg"
                        ]
                    ]
                ],
                "footer" => [
                    "type" => "box",
                    "layout" => "vertical",
                    "contents" => [
                        [
                            "type" => "button",
                            "action" => [
                                "type" => "uri",
                                "label" => "รายละเอียด",
                                "uri" => "https://liff.line.me/1660802157-KM5m1bE9"
                            ],
                            "style" => "secondary"
                        ]
                    ]
                ]
            ]

        ];

        $LINEDatas['url'] = "https://api.line.me/v2/bot/message/push";
        $LINEDatas['token'] = env("LINE_ACCESS_TOKEN");

        $messages = [];
        $messages['to'] = $LineUUID;
        $messages['messages'][0] = $jayParsedAry;
        $encodeJson = json_encode($messages);
        $results = $this->sentMessage($encodeJson, $LINEDatas);
        return response()->json($results);
    }

    public function notify_message($token, $message)
    {
        $LINE_API = "https://notify-api.line.me/api/notify";
        $LINE_TOKEN = $token;
        $queryData = array('message' => $message);
        $queryData = http_build_query($queryData, '', '&');
        $headerOptions = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
                    . "Authorization: Bearer " . $LINE_TOKEN . "\r\n"
                    . "Content-Length: " . strlen($queryData) . "\r\n",
                'content' => $queryData,
                'ignore_errors' => true
            )
        );
        $context = stream_context_create($headerOptions);
        $result = file_get_contents($LINE_API, FALSE, $context);
        $res = json_decode($result);
        return $res;
    }
}
