<?php

namespace App\Http\Controllers;

use App\JobEquipment;
use Illuminate\Http\Request;

class JobEquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $output = JobEquipment::get();
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
        $credentials = $request->all();
        JobEquipment::create($credentials);
        return response()->json([
            'status' => true,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\JobEquipment  $jobEquipment
     * @return \Illuminate\Http\Response
     */
    public function show(JobEquipment $jobEquipment)
    {
        return response()->json($jobEquipment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\JobEquipment  $jobEquipment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobEquipment $jobEquipment)
    {
        $credentials = $request->all();
        $jobEquipment->update($credentials);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\JobEquipment  $jobEquipment
     * @return \Illuminate\Http\Response
     */
    public function destroy(JobEquipment $jobEquipment)
    {
        $jobEquipment->delete();
    }

    public function getByJobId($job_id)
    {
        $output = JobEquipment::with('equipment')
            ->where('job_id', $job_id)
            ->get();
        return response()->json($output);
    }
}
