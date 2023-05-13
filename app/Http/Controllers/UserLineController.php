<?php

namespace App\Http\Controllers;

use App\UserLine;
use Illuminate\Http\Request;

class UserLineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $output = UserLine::get();
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
        UserLine::create($credentials);

        $data['name'] = $credentials['name'];
        $data['email'] = $credentials['name'];
        $data['level'] = $credentials['name'];
        $data['name'] = $credentials['name'];
        $data['name'] = $credentials['name'];
        $data['name'] = $credentials['name'];
        $data['name'] = $credentials['name'];

        return response()->json([
            'status' => true,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserLine  $userLine
     * @return \Illuminate\Http\Response
     */
    public function show(UserLine $userLine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserLine  $userLine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserLine $userLine)
    {
        $credentials = $request->all();
        $userLine->update($credentials);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserLine  $userLine
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserLine $userLine)
    {
        //
    }
}
