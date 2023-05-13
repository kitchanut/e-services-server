<?php

namespace App\Http\Controllers;

use App\JobResult;
use App\Narcotic;
use App\Timeline;
use App\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NarcoticController extends Controller
{

    public function index()
    {
        //
    }

    public function countData(Request $request)
    {

        $pedding = Narcotic::where([['status', 'pedding']])->count();
        $accept = Narcotic::where([['status', 'accept']])->count();
        $close = Narcotic::where([['status', 'close']])->count();
        $cancle = Narcotic::where([['status', 'cancle']])->count();
        $all = Narcotic::count();

        $output['pedding'] = $pedding;
        $output['accept'] = $accept;
        $output['close'] = $close;
        $output['cancle'] = $cancle;
        $output['all'] = $all;

        return response()->json($output);
    }

    public function indexCustom(Request $request)
    {
        $toggle = $request->input('toggle');
        if ($toggle == 'all') {
            $toggle = null;
        }

        $output = Narcotic::with('user', 'uploads')
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
        $credentials['number'] =  $this->getPrefix('IN');
        $credentials['status'] = 'pedding';
        $created = Narcotic::create($credentials);
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $key => $file) {
                $path = $file->store('files');
                $dataFile['type'] = 'narcotic';
                $dataFile['ref_id'] = $created->id;
                $dataFile['name'] = $file->getClientOriginalName();
                $dataFile['extension'] = $file->getClientOriginalExtension();
                $dataFile['size'] = $file->getSize();
                $dataFile['path'] = $path;
                Upload::create($dataFile);
            }
        }

        $timeline['ref_id'] = $created->id;
        $timeline['type'] = 'narcotic';
        $timeline['title'] = "แจ้งเรื่อง";
        $timeline['user_id'] = $credentials['user_id'];
        $TimelineCreate = Timeline::create($timeline);

        $LineUUID = $credentials['lineUUID'];
        $title = 'แจ้งเบาะแสยาเสพติด';
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
     * @param  \App\Narcotic  $narcotic
     * @return \Illuminate\Http\Response
     */
    public function show(Narcotic $narcotic)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Narcotic  $narcotic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Narcotic $narcotic)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Narcotic  $narcotic
     * @return \Illuminate\Http\Response
     */
    public function destroy(Narcotic $narcotic)
    {
        $uplods = Upload::where([['type', 'narcotic'], ['ref_id', $narcotic->id]])->get();
        foreach ($uplods as $key => $value) {
            Storage::delete($value->path);
            Upload::where('id', $value->id)->delete();
        }
        Timeline::where([['type', 'narcotic'], ['ref_id', $narcotic->id]])->delete();

        $JobResult = JobResult::where([['type', 'narcotic'], ['ref_id', $narcotic->id]])->first();
        if ($JobResult) {
            $uplodJobResult = Upload::where([['type', 'job_result'], ['ref_id', $JobResult->id]])->get();
            foreach ($uplodJobResult as $key => $value) {
                Storage::delete($value->path);
                Upload::where('id', $value->id)->delete();
            }
            JobResult::where([['type', 'narcotic'], ['ref_id', $narcotic->id]])->delete();
        }

        $narcotic->delete();
    }
}
