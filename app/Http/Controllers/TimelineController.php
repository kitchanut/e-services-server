<?php

namespace App\Http\Controllers;

use App\Corruption;
use App\Fire;
use App\Job;
use App\JobEquipment;
use App\JobResult;
use App\Narcotic;
use App\Project;
use App\RequestInformation;
use App\Timeline;
use App\User;
use App\UserTechnician;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class TimelineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $output = Timeline::with('user')
            ->where('title', 'แจ้งเรื่อง')
            ->limit(1000)
            ->orderBy('created_at', 'DESC')
            ->get();

        $output->map(function ($item) {
            if ($item->type == 'inform') {
                $item['details'] = Fire::find($item->ref_id);
            }
            return $item;
        });
        return response()->json($output);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Timeline  $timeline
     * @return \Illuminate\Http\Response
     */
    public function show(Timeline $timeline)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Timeline  $timeline
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Timeline $timeline)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Timeline  $timeline
     * @return \Illuminate\Http\Response
     */
    public function destroy(Timeline $timeline)
    {
        //
    }

    public function showDetails($type, $id)
    {


        if ($type == 'fire' || $type == 'pole' || $type == 'water' || $type == 'road' || $type == 'ems') {
            $timeline = Timeline::with('user')
                ->where('type', 'inform')
                ->where('ref_id', $id)
                ->get();

            $data = Fire::with('user', 'uploads')
                ->where('id', $id)
                ->first();
        } elseif ($type == 'corruption') {

            $timeline = Timeline::with('user')
                ->where('type', $type)
                ->where('ref_id', $id)
                ->get();

            $data = Corruption::with('user', 'uploads')
                ->where('id', $id)
                ->first();
        } elseif ($type == 'narcotic') {
            $timeline = Timeline::with('user')
                ->where('type', $type)
                ->where('ref_id', $id)
                ->get();

            $data = Narcotic::with('user', 'uploads')
                ->where('id', $id)
                ->first();
        } elseif ($type == 'request_information') {
            $timeline = Timeline::with('user')
                ->where('type', $type)
                ->where('ref_id', $id)
                ->get();

            $data = RequestInformation::with('user', 'uploads')
                ->where('id', $id)
                ->first();
        } elseif ($type == 'project') {
            $timeline = Timeline::with('user')
                ->where('type', $type)
                ->where('ref_id', $id)
                ->get();

            $data = Project::with('user', 'uploads')
                ->where('id', $id)
                ->first();
        }


        $job = Job::where('type', $type)
            ->where('ref_id', $id)
            ->first();

        $result = JobResult::with('uploads')
            ->where('type', $type)
            ->where('ref_id', $id)
            ->first();

        $result_equipment = [];
        if ($job) {

            $result_equipment = JobEquipment::with('equipment')
                ->where('job_id', $job->id)
                ->get();
        }

        if ($job) {
            foreach (json_decode($job->user_technician_id) as $key => $value) {
                $UserTechnician = User::find($value);
                if ($key == 0) {
                    $job['technician'] = $UserTechnician->name;
                } else {
                    $job['technician'] .= ' | ' . $UserTechnician->name;
                }
            }
        }

        return response()->json([
            'timeline' => $timeline,
            'data' => $data,
            'job' => $job,
            'result' => $result,
            'result_equipment' => $result_equipment
        ]);
    }

    public function accept(Request $request, $type, $id)
    {
        if ($type == 'fire' || $type == 'pole' || $type == 'water' || $type == 'road' || $type == 'ems') {
            $data = Fire::find($id);
            $data->status = 'accept';
            $data->save();

            $timeline['type'] = 'inform';


            switch ($type) {
                case "fire":
                    $title = 'แจ้งเหตุเพลิงไหม้';
                    break;
                case "pole":
                    $title = 'แจ้งเหตุไฟส่องสว่างชำรุด';
                    break;
                case "water":
                    $title = 'แจ้งประปาชำรุด';
                    break;
                case "road":
                    $title = 'แจ้งชำรุดงานทาง';
                    break;
                case "ems":
                    $title = 'ฉุกเฉิน (EMS)';
                    break;
                default:
                    $title = 'Error';
            }
            $LineUUID = $data->lineUUID;
            $number = $data->number;
            $date_time = explode(" ", $data->created_at);
            $informDate = $date_time[0];
            $informTime = $date_time[1];
        } elseif ($type == 'corruption') {
            $data = Corruption::find($id);
            $data->status = 'accept';
            $data->save();

            $timeline['type'] = $type;

            $title = 'แจ้งทุจริตและประพฤติมิชอบ';
            $LineUUID = $data->lineUUID;
            $number = $data->number;
            $date_time = explode(" ", $data->created_at);
            $informDate = $date_time[0];
            $informTime = $date_time[1];
        } elseif ($type == 'narcotic') {
            $data = Narcotic::find($id);
            $data->status = 'accept';
            $data->save();

            $timeline['type'] = $type;

            $title = 'แจ้งเบาะแสยาเสพติด';
            $LineUUID = $data->lineUUID;
            $number = $data->number;
            $date_time = explode(" ", $data->created_at);
            $informDate = $date_time[0];
            $informTime = $date_time[1];
        } elseif ($type == 'request_information') {
            $data = RequestInformation::find($id);
            $data->status = 'accept';
            $data->save();

            $timeline['type'] = $type;

            $title = 'คำร้องขอข้อมูลข่าวสาร';
            $LineUUID = $data->lineUUID;
            $number = $data->number;
            $date_time = explode(" ", $data->created_at);
            $informDate = $date_time[0];
            $informTime = $date_time[1];
        } elseif ($type == 'project') {
            $data = Project::find($id);
            $data->status = 'accept';
            $data->save();

            $timeline['type'] = $type;

            $title = 'เสนอแผนพัฒนาท้องถิ่น';
            $LineUUID = $data->lineUUID;
            $number = $data->number;
            $date_time = explode(" ", $data->created_at);
            $informDate = $date_time[0];
            $informTime = $date_time[1];
        }


        $timeline['ref_id'] = $id;
        $timeline['title'] = "รับแจ้ง";
        $timeline['user_id'] = auth()->user()->id;
        $TimelineCreate = Timeline::create($timeline);

        $accept_datetime = explode(" ", $TimelineCreate->created_at);
        $acceptDate = $accept_datetime[0];
        $acceptTime = $accept_datetime[1];

        $this->sentCardAccept($LineUUID, $title, $number, $informDate, $informTime,  $acceptDate, $acceptTime);
    }

    public function cancleItem(Request $request, $type, $id)
    {

        if ($type == 'fire' || $type == 'pole' || $type == 'water' || $type == 'road' || $type == 'ems') {
            $data = Fire::find($id);
            $data->status = 'cancle';
            $data->save();

            $job = Job::where([['type', $type], ['ref_id', $id]])->first();
            if ($job) {
                $job->status = 'cancle';
                $job->save();
            }

            $timeline['type'] = 'inform';
        } elseif ($type == 'corruption') {
            $data = Corruption::find($id);
            $data->status = 'cancle';
            $data->save();

            $timeline['type'] = $type;
        } elseif ($type == 'narcotic') {
            $data = Narcotic::find($id);
            $data->status = 'cancle';
            $data->save();

            $timeline['type'] = $type;
        } elseif ($type == 'request_information') {
            $data = RequestInformation::find($id);
            $data->status = 'cancle';
            $data->save();

            $timeline['type'] = $type;
        } elseif ($type == 'project') {
            $data = Project::find($id);
            $data->status = 'cancle';
            $data->save();

            $timeline['type'] = $type;
        }


        $timeline['ref_id'] = $id;
        $timeline['title'] = "ยกเลิกรายการ";
        $timeline['details'] = $request->message;
        $timeline['user_id'] = auth()->user()->id;
        Timeline::create($timeline);
    }

    public function sentJob(Request $request, $type, $id)
    {
        $data = Fire::find($id);
        $data->status = 'sentJob';
        $data->save();

        $timelineQuery = Timeline::where([['type', 'inform'], ['ref_id', $id], ['title', 'รับแจ้ง']])->first();

        $job = Job::where([['type', $type], ['ref_id', $id]])->first();
        $jobUpdate['receive_date'] = $timelineQuery->created_at;
        $jobUpdate['jobsent_date'] = date("Y-m-d H:i:s");
        $jobUpdate['status'] = 'sentJob';
        $job->update($jobUpdate);

        $timeline['type'] = 'inform';
        $timeline['ref_id'] = $id;
        $timeline['title'] = "สั่งการ";
        $timeline['details'] = '';
        foreach (json_decode($job->user_technician_id) as $key => $value) {
            $UserTechnician = User::find($value);
            if (isset($UserTechnician->notify_token) && $UserTechnician->notify_token != '') {
                $this->notify_message($UserTechnician->notify_token, $job->job_number . " มีงานใหม่กรุณาตรวจสอบในระบบ หรือคลิก https://liff.line.me/1660802157-VQ4doBLK");
            }
            if ($key == 0) {
                $timeline['details'] = $UserTechnician->name;
            } else {
                $timeline['details'] .= ' | ' . $UserTechnician->name;
            }
        }
        $timeline['user_id'] = auth()->user()->id;
        Timeline::create($timeline);
    }

    public function close(Request $request, $type, $id)
    {

        if ($type == 'fire' || $type == 'pole' || $type == 'water' || $type == 'road' || $type == 'ems') {
            $data = Fire::find($id);
            $data->status = 'close';
            $data->save();

            $job = Job::where([['type', $type], ['ref_id', $id]])->first();
            $jobUpdate['status'] = 'close';
            $job->update($jobUpdate);

            $timeline['type'] = 'inform';

            switch ($type) {
                case "fire":
                    $title = 'แจ้งเหตุเพลิงไหม้';
                    break;
                case "pole":
                    $title = 'แจ้งเหตุไฟส่องสว่างชำรุด';
                    break;
                case "water":
                    $title = 'แจ้งประปาชำรุด';
                    break;
                case "road":
                    $title = 'แจ้งชำรุดงานทาง';
                    break;
                case "ems":
                    $title = 'ฉุกเฉิน (EMS)';
                    break;
                default:
                    $title = 'Error';
            }
            $LineUUID = $data->lineUUID;
            $number = $data->number;
            $date_time = explode(" ", $data->created_at);
            $informDate = $date_time[0];
            $informTime = $date_time[1];

            $timelineAccept = Timeline::where([['type', 'inform'], ['ref_id', $id], ['title', 'รับแจ้ง']])->first();
            $accept_datetime = explode(" ", $timelineAccept->created_at);
            $acceptDate = $accept_datetime[0];
            $acceptTime = $accept_datetime[1];
        } elseif ($type == 'corruption') {
            $data = Corruption::find($id);
            $data->status = 'close';
            $data->save();

            $timeline['type'] = $type;

            $title = 'แจ้งทุจริตและประพฤติมิชอบ';
            $LineUUID = $data->lineUUID;
            $number = $data->number;
            $date_time = explode(" ", $data->created_at);
            $informDate = $date_time[0];
            $informTime = $date_time[1];

            $timelineAccept = Timeline::where([['type', $type], ['ref_id', $id], ['title', 'รับแจ้ง']])->first();
            $accept_datetime = explode(" ", $timelineAccept->created_at);
            $acceptDate = $accept_datetime[0];
            $acceptTime = $accept_datetime[1];
        } elseif ($type == 'narcotic') {
            $data = Narcotic::find($id);
            $data->status = 'close';
            $data->save();

            $timeline['type'] = $type;

            $title = 'แจ้งเบาะแสยาเสพติด';
            $LineUUID = $data->lineUUID;
            $number = $data->number;
            $date_time = explode(" ", $data->created_at);
            $informDate = $date_time[0];
            $informTime = $date_time[1];

            $timelineAccept = Timeline::where([['type', $type], ['ref_id', $id], ['title', 'รับแจ้ง']])->first();
            $accept_datetime = explode(" ", $timelineAccept->created_at);
            $acceptDate = $accept_datetime[0];
            $acceptTime = $accept_datetime[1];
        } elseif ($type == 'request_information') {
            $data = RequestInformation::find($id);
            $data->status = 'close';
            $data->save();

            $timeline['type'] = $type;

            $title = 'คำร้องขอข้อมูลข่าวสาร';
            $LineUUID = $data->lineUUID;
            $number = $data->number;
            $date_time = explode(" ", $data->created_at);
            $informDate = $date_time[0];
            $informTime = $date_time[1];

            $timelineAccept = Timeline::where([['type', $type], ['ref_id', $id], ['title', 'รับแจ้ง']])->first();
            $accept_datetime = explode(" ", $timelineAccept->created_at);
            $acceptDate = $accept_datetime[0];
            $acceptTime = $accept_datetime[1];
        } elseif ($type == 'project') {
            $data = Project::find($id);
            $data->status = 'close';
            $data->save();

            $timeline['type'] = $type;

            $title = 'เสนอแผนพัฒนาท้องถิ่น';
            $LineUUID = $data->lineUUID;
            $number = $data->number;
            $date_time = explode(" ", $data->created_at);
            $informDate = $date_time[0];
            $informTime = $date_time[1];

            $timelineAccept = Timeline::where([['type', $type], ['ref_id', $id], ['title', 'รับแจ้ง']])->first();
            $accept_datetime = explode(" ", $timelineAccept->created_at);
            $acceptDate = $accept_datetime[0];
            $acceptTime = $accept_datetime[1];
        }


        $timeline['ref_id'] = $id;
        $timeline['title'] = "ปิดงาน";
        $timeline['user_id'] = auth()->user()->id;
        $TimelineCreate = Timeline::create($timeline);

        $close_datetime = explode(" ", $TimelineCreate->created_at);
        $closeDate = $close_datetime[0];
        $closeTime = $close_datetime[1];

        $this->sentCardClose($LineUUID, $title, $number, $informDate, $informTime,  $acceptDate, $acceptTime, $closeDate, $closeTime);
    }
}
