<?php

namespace App\Http\Controllers;

use App\Corruption;
use App\JobResult;
use App\Timeline;
use App\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CorruptionController extends Controller
{

    public function index()
    {
        //
    }

    public function countData(Request $request)
    {

        $pedding = Corruption::where([['status', 'pedding']])->count();
        $accept = Corruption::where([['status', 'accept']])->count();
        $close = Corruption::where([['status', 'close']])->count();
        $cancle = Corruption::where([['status', 'cancle']])->count();
        $all = Corruption::count();

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

        $output = Corruption::with('user', 'uploads')
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
        $created = Corruption::create($credentials);
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $key => $file) {
                $path = $file->store('files');
                $dataFile['type'] = 'corruption';
                $dataFile['ref_id'] = $created->id;
                $dataFile['name'] = $file->getClientOriginalName();
                $dataFile['extension'] = $file->getClientOriginalExtension();
                $dataFile['size'] = $file->getSize();
                $dataFile['path'] = $path;
                Upload::create($dataFile);
            }
        }

        $timeline['ref_id'] = $created->id;
        $timeline['type'] = 'corruption';
        $timeline['title'] = "แจ้งเรื่อง";
        $timeline['user_id'] = $credentials['user_id'];
        $TimelineCreate = Timeline::create($timeline);

        $LineUUID = $credentials['lineUUID'];
        $title = 'แจ้งทุจริตและประพฤติมิชอบ';
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
     * @param  \App\Corruption  $corruption
     * @return \Illuminate\Http\Response
     */
    public function show(Corruption $corruption)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Corruption  $corruption
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Corruption $corruption)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Corruption  $corruption
     * @return \Illuminate\Http\Response
     */
    public function destroy(Corruption $corruption)
    {
        $uplods = Upload::where([['type', 'corruption'], ['ref_id', $corruption->id]])->get();
        foreach ($uplods as $key => $value) {
            Storage::delete($value->path);
            Upload::where('id', $value->id)->delete();
        }
        Timeline::where([['type', 'corruption'], ['ref_id', $corruption->id]])->delete();

        $JobResult = JobResult::where([['type', 'corruption'], ['ref_id', $corruption->id]])->first();
        if ($JobResult) {
            $uplodJobResult = Upload::where([['type', 'job_result'], ['ref_id', $JobResult->id]])->get();
            foreach ($uplodJobResult as $key => $value) {
                Storage::delete($value->path);
                Upload::where('id', $value->id)->delete();
            }
            JobResult::where([['type', 'corruption'], ['ref_id', $corruption->id]])->delete();
        }
        $corruption->delete();
    }
}
