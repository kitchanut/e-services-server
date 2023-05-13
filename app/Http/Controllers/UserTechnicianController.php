<?php

namespace App\Http\Controllers;

use App\UserTechnician;
use Illuminate\Http\Request;

class UserTechnicianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $output = UserTechnician::get();
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
        UserTechnician::create($credentials);
        return response()->json([
            'status' => true,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserTechnician  $userTechnician
     * @return \Illuminate\Http\Response
     */
    public function show(UserTechnician $userTechnician)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserTechnician  $userTechnician
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserTechnician $userTechnician)
    {
        $credentials = $request->all();
        $userTechnician->update($credentials);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserTechnician  $userTechnician
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserTechnician $userTechnician)
    {
        //
    }
}
