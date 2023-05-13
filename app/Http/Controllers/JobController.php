<?php

namespace App\Http\Controllers;

use App\Fire;
use App\Job;
use App\Prefix;
use App\Timeline;
use App\User;
use App\UserTechnician;
use Illuminate\Http\Request;

class JobController extends Controller
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
        $credentials = $request->all();
        $dataUpdate['user_technician_id'] = $credentials['user_technician_id'];

        $type_prefix = "JT";
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
        $credentials['job_number'] =  $number;
        $credentials['user_id'] = $request->user()->id;
        $credentials['user_technician_id'] = '[]';
        $created = Job::create($credentials);
        $created->update($dataUpdate);

        $data = Fire::find($credentials['ref_id']);
        $data->status = 'selectTechnician';
        $data->save();

        $timeline['type'] = 'inform';
        $timeline['details'] = '';
        foreach ($dataUpdate['user_technician_id'] as $key => $value) {
            $UserTechnician = User::find($value);
            if ($key == 0) {
                $timeline['details'] = $UserTechnician->name;
            } else {
                $timeline['details'] .= ' | ' . $UserTechnician->name;
            }
        }
        $timeline['ref_id'] = $credentials['ref_id'];
        $timeline['title'] = "เลือกผู้รับผิดชอบ";
        $timeline['user_id'] = $request->user()->id;
        Timeline::create($timeline);

        return response()->json([
            'status' => true,
            'data' => $credentials
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function show(Job $job)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Job $job)
    {
        $credentials = $request->all();

        $timeline['type'] = 'inform';
        $timeline['details'] = '';
        foreach ($credentials['user_technician_id'] as $key => $value) {
            $UserTechnician = User::find($value);
            if ($key == 0) {
                $timeline['details'] = $UserTechnician->name;
            } else {
                $timeline['details'] .= ' | ' . $UserTechnician->name;
            }
        }
        $timeline['ref_id'] = $credentials['ref_id'];
        $timeline['title'] = "แก้ไขผู้รับผิดชอบ";
        $timeline['user_id'] = $request->user()->id;
        Timeline::create($timeline);

        $job->update($credentials);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function destroy(Job $job)
    {
        //
    }

    public function checkJob($type, $id)
    {
        $output = Job::where('type', $type)
            ->where('ref_id', $id)
            ->first();
        return response()->json($output);
    }

    public function getByTechnician($user_technician_id)
    {
        $output =  Job::where('user_technician_id', 'LIKE', '%' . $user_technician_id . '%')
            ->where('status', '!=', 'close')
            ->where('status', '!=', 'created')
            ->where('status', '!=', 'cancle')
            ->orderBy('created_at', 'DESC')
            ->get();
        $map = $output->map(function ($item) {
            $item['details'] = Fire::with('uploads', 'user')->find($item->ref_id);
            foreach (json_decode($item->user_technician_id) as $key => $value) {
                $UserTechnician = User::find($value);
                if ($key == 0) {
                    $item['technician'] = $UserTechnician->name;
                } else {
                    $item['technician'] .= ' | ' . $UserTechnician->name;
                }
            }
            return  $item;
        });
        return response()->json($map);
    }

    public function cancleItem(Request $request, $type, $id)
    {
        $data = Fire::find($id);
        $data->status = 'reject';
        $data->save();

        $job = Job::where([['type', $type], ['ref_id', $id]])->first();
        $job->status = 'reject';
        $job->save();

        $timeline['type'] = 'inform';
        $timeline['ref_id'] = $id;
        $timeline['title'] = "ปฏิเสธงาน";
        $timeline['details'] = $request->message;
        $timeline['user_id'] = $request->user_technician_id;
        Timeline::create($timeline);
    }

    public function action(Request $request, $type, $id, $actionType)
    {
        $job = Job::where([['type', $type], ['ref_id', $id]])->first();
        if ($actionType == 'acceptJob') {

            $data = Fire::find($id);
            $data->status = 'acceptJob';
            $data->save();

            $job->accept_date = date("Y-m-d H:i:s");
            $job->accept_lat = $request->lat;
            $job->accept_lng = $request->lng;
            $job->status = 'acceptJob';
            $job->save();
            $timeline['title'] = "รับงาน";
        } elseif ($actionType == 'start') {
            $job->start_date = date("Y-m-d H:i:s");
            $job->start_lat = $request->lat;
            $job->start_lng = $request->lng;
            $job->status = 'start';
            $job->save();
            $timeline['title'] = "ออกจากฐาน";
        } elseif ($actionType == 'arrive') {
            $job->arrive_date = date("Y-m-d H:i:s");
            $job->arrive_lat = $request->lat;
            $job->arrive_lng = $request->lng;
            $job->status = 'arrive';
            $job->save();
            $timeline['title'] = "ถึงสถานที่แจ้ง";
        } elseif ($actionType == 'finish') {
            $job->finish_date = date("Y-m-d H:i:s");
            $job->finish_lat = $request->lat;
            $job->finish_lng = $request->lng;
            $job->status = 'finish';
            $job->save();
            $timeline['title'] = "ออกจากที่แจ้ง";
        } elseif ($actionType == 'hospital') {
            $job->hospital_date = date("Y-m-d H:i:s");
            $job->status = 'hospital';
            $job->hospital_lat = $request->lat;
            $job->hospital_lng = $request->lng;
            $job->save();
            $timeline['title'] = "ถึง ร.พ.";

            $data = Fire::find($id);
            $data->status = 'finish';
            $data->save();
        } elseif ($actionType == 'office') {
            $job->office_date = date("Y-m-d H:i:s");
            $job->status = 'office';
            $job->office_lat = $request->lat;
            $job->office_lng = $request->lng;
            $job->save();
            $timeline['title'] = "ถึงฐาน";

            $data = Fire::find($id);
            $data->status = 'finish';
            $data->save();
        }

        $timeline['type'] = 'inform';
        $timeline['ref_id'] = $id;
        $timeline['user_id'] = $request->user_technician_id;
        Timeline::create($timeline);
    }
}
