<?php

namespace App\Http\Controllers;

use App\JobResult;
use App\Timeline;
use App\Upload;
use Illuminate\Http\Request;

class JobResultController extends Controller
{

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $credentials = (array) json_decode($data['formData']);
        if (isset($credentials['user_technician_id'])) {
            $user_technician_id = $credentials['user_technician_id'];
            unset($credentials['user_technician_id']);
        }
        $created = JobResult::create($credentials);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $key => $file) {
                $path = $file->store('files');
                $dataFile['type'] = "job_result";
                $dataFile['ref_id'] = $created->id;
                $dataFile['name'] = $file->getClientOriginalName();
                $dataFile['extension'] = $file->getClientOriginalExtension();
                $dataFile['size'] = $file->getSize();
                $dataFile['path'] = $path;
                Upload::create($dataFile);
            }
        }

        if ($credentials['type'] == 'fire' || $credentials['type'] == 'pole' || $credentials['type'] == 'water' || $credentials['type'] == 'road' || $credentials['type'] == 'ems') {
            $timeline['type'] = 'inform';
        } else {
            $timeline['type'] = $credentials['type'];
        }
        $timeline['ref_id'] = $credentials['ref_id'];
        $timeline['title'] = "เพิ่มผลการดำเนินการ";
        $timeline['details'] = $credentials['result'];
        if (isset($credentials['user_technician_id'])) {
            $timeline['user_technician_id'] = $user_technician_id;
        } else {
            $timeline['user_id'] = $request->user()->id;
        }
        Timeline::create($timeline);

        return response()->json([
            'status' => true,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\JobResult  $jobResult
     * @return \Illuminate\Http\Response
     */
    public function show(JobResult $jobResult)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\JobResult  $jobResult
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobResult $jobResult)
    {
        $data = $request->all();
        $credentials = (array) json_decode($data['formData']);
        if (isset($credentials['user_technician_id'])) {
            $user_technician_id = $credentials['user_technician_id'];
            unset($credentials['user_technician_id']);
        }
        $jobResult->update($credentials);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $key => $file) {
                $path = $file->store('files');
                $dataFile['type'] = "job_result";
                $dataFile['ref_id'] = $jobResult->id;
                $dataFile['name'] = $file->getClientOriginalName();
                $dataFile['extension'] = $file->getClientOriginalExtension();
                $dataFile['size'] = $file->getSize();
                $dataFile['path'] = $path;
                Upload::create($dataFile);
            }
        }

        if ($credentials['type'] == 'fire' || $credentials['type'] == 'pole' || $credentials['type'] == 'water' || $credentials['type'] == 'road' || $credentials['type'] == 'ems') {
            $timeline['type'] = 'inform';
        } else {
            $timeline['type'] = $credentials['type'];
        }
        $timeline['ref_id'] = $credentials['ref_id'];
        $timeline['title'] = "แก้ไขผลการดำเนินการ";
        $timeline['details'] = $credentials['result'];
        if (isset($credentials['user_technician_id'])) {
            $timeline['user_technician_id'] = $user_technician_id;
        } else {
            $timeline['user_id'] = $request->user()->id;
        }
        Timeline::create($timeline);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\JobResult  $jobResult
     * @return \Illuminate\Http\Response
     */
    public function destroy(JobResult $jobResult)
    {
        //
    }

    public function check($type, $ref_id)
    {
        $output = JobResult::with('uploads')
            ->where('type', $type)
            ->where('ref_id', $ref_id)
            ->first();
        return response()->json($output);
    }
}
