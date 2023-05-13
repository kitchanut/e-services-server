<?php

namespace App\Http\Controllers;

use App\Fire;
use App\Job;
use App\JobEquipment;
use App\JobResult;
use App\Prefix;
use App\Timeline;
use App\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FireController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $output = Fire::with('user', 'uploads')->get();
        return response()->json($output);
    }

    public function countData(Request $request, $type)
    {

        $pedding = Fire::where([['type', $type], ['status', 'pedding']])->count();
        $accept = Fire::where([['type', $type], ['status', 'accept']])->count();
        $selectTechnician = Fire::where([['type', $type], ['status', 'selectTechnician']])->count();
        $sentJob = Fire::where([['type', $type], ['status', 'sentJob']])->count();
        $reject = Fire::where([['type', $type], ['status', 'reject']])->count();
        $acceptJob = Fire::where([['type', $type], ['status', 'acceptJob']])->count();
        $finish = Fire::where([['type', $type], ['status', 'finish']])->count();
        $close = Fire::where([['type', $type], ['status', 'close']])->count();
        $cancle = Fire::where([['type', $type], ['status', 'cancle']])->count();
        $all = Fire::where([['type', $type]])->count();

        $output['pedding'] = $pedding;
        $output['accept'] = $accept;
        $output['selectTechnician'] = $selectTechnician;
        $output['sentJob'] = $sentJob;
        $output['reject'] = $reject;
        $output['acceptJob'] = $acceptJob;
        $output['finish'] = $finish;
        $output['close'] = $close;
        $output['cancle'] = $cancle;
        $output['all'] = $all;

        return response()->json($output);
    }

    public function indexCustom(Request $request)
    {
        $toggle = $request->input('toggle');
        $type = $request->input('type');
        if ($toggle == 'all') {
            $toggle = null;
        }

        $output = Fire::with('user', 'uploads')
            ->where('type', $type)
            ->when($toggle, function ($query) use ($toggle) {
                $query->where('status', (string) $toggle);
            })
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json($output);
    }

    public function store(Request $request)
    {

        $data = $request->all();
        $credentials = (array) json_decode($data['formData']);
        $credentials['status'] = 'pedding';
        $credentials['number'] =  $this->getPrefix('IN');

        $created = Fire::create($credentials);
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $key => $file) {
                $path = $file->store('files');
                $dataFile['type'] = 'inform';
                $dataFile['ref_id'] = $created->id;
                $dataFile['name'] = $file->getClientOriginalName();
                $dataFile['extension'] = $file->getClientOriginalExtension();
                $dataFile['size'] = $file->getSize();
                $dataFile['path'] = $path;
                Upload::create($dataFile);
            }
        }

        $timeline['ref_id'] = $created->id;
        $timeline['type'] = 'inform';
        $timeline['title'] = "แจ้งเรื่อง";
        $timeline['user_id'] = $request->user()->id;
        $TimelineCreate = Timeline::create($timeline);

        switch ($credentials['type']) {
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

        $LineUUID = $credentials['lineUUID'];
        $number = $credentials['number'];
        $inform_datetime = explode(" ", $TimelineCreate->created_at);
        $informDate = $inform_datetime[0];
        $informTime = $inform_datetime[1];

        $this->sentCardInform($LineUUID, $title, $number, $informDate, $informTime);


        return response()->json([
            'status' => true,
            'data' => $created,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Fire  $fire
     * @return \Illuminate\Http\Response
     */
    public function show(Fire $fire)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Fire  $fire
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Fire $fire)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Fire  $fire
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fire $fire)
    {
        $uplods = Upload::where([['type', 'inform'], ['ref_id', $fire->id]])->get();
        foreach ($uplods as $key => $value) {
            Storage::delete($value->path);
            Upload::where('id', $value->id)->delete();
        }
        $Job = Job::where([['ref_id', $fire->id], ['type', $fire->type]])->first();
        if ($Job) {
            $JobResult = JobResult::where([['type', $fire->type], ['ref_id', $fire->id]])->first();
            if ($JobResult) {
                $uplods_result = Upload::where([['type', 'job_result'], ['ref_id', $JobResult->id]])->get();
                foreach ($uplods_result as $key => $value) {
                    Storage::delete($value->path);
                    Upload::where('id', $value->id)->delete();
                }
            }

            JobEquipment::where([['job_id', $Job->id]])->delete();
        }

        Job::where([['ref_id', $fire->id], ['type', $fire->type]])->delete();
        JobResult::where([['ref_id', $fire->id], ['type', $fire->type]])->delete();
        Timeline::where([['ref_id', $fire->id], ['type', 'inform']])->delete();

        $fire->delete();
    }
}
