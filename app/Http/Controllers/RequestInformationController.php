<?php

namespace App\Http\Controllers;

use App\JobResult;
use App\RequestInformation;
use App\Timeline;
use App\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequestInformationController extends Controller
{

    public function index()
    {
        //
    }

    public function countData(Request $request)
    {

        $pedding = RequestInformation::where([['status', 'pedding']])->count();
        $accept = RequestInformation::where([['status', 'accept']])->count();
        $close = RequestInformation::where([['status', 'close']])->count();
        $cancle = RequestInformation::where([['status', 'cancle']])->count();
        $all = RequestInformation::count();

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

        $output = RequestInformation::with('user', 'uploads')
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
        if (isset($credentials['receive_date'])) {
            $credentials['receive_date'] = date('Y-m-d H:i:s', strtotime($credentials['receive_date']));
        }
        $credentials['number'] =  $this->getPrefix('IN');
        $credentials['status'] = 'pedding';
        $created = RequestInformation::create($credentials);
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $key => $file) {
                $path = $file->store('files');
                $dataFile['type'] = 'request_information';
                $dataFile['ref_id'] = $created->id;
                $dataFile['name'] = $file->getClientOriginalName();
                $dataFile['extension'] = $file->getClientOriginalExtension();
                $dataFile['size'] = $file->getSize();
                $dataFile['path'] = $path;
                Upload::create($dataFile);
            }
        }

        $timeline['ref_id'] = $created->id;
        $timeline['type'] = 'request_information';
        $timeline['title'] = "แจ้งเรื่อง";
        $timeline['user_id'] = $credentials['user_id'];
        $TimelineCreate = Timeline::create($timeline);

        $LineUUID = $credentials['lineUUID'];
        $title = 'คำร้องขอข้อมูลข่าวสาร';
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
     * @param  \App\RequestInformation  $requestInformation
     * @return \Illuminate\Http\Response
     */
    public function show(RequestInformation $requestInformation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RequestInformation  $requestInformation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RequestInformation $requestInformation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RequestInformation  $requestInformation
     * @return \Illuminate\Http\Response
     */
    public function destroy(RequestInformation $requestInformation)
    {
        $uplods = Upload::where([['type', 'request_information'], ['ref_id', $requestInformation->id]])->get();
        foreach ($uplods as $key => $value) {
            Storage::delete($value->path);
            Upload::where('id', $value->id)->delete();
        }
        Timeline::where([['type', 'request_information'], ['ref_id', $requestInformation->id]])->delete();

        $JobResult = JobResult::where([['type', 'request_information'], ['ref_id', $requestInformation->id]])->first();
        if ($JobResult) {
            $uplodJobResult = Upload::where([['type', 'job_result'], ['ref_id', $JobResult->id]])->get();
            foreach ($uplodJobResult as $key => $value) {
                Storage::delete($value->path);
                Upload::where('id', $value->id)->delete();
            }
            JobResult::where([['type', 'request_information'], ['ref_id', $requestInformation->id]])->delete();
        }

        $requestInformation->delete();
    }
}
