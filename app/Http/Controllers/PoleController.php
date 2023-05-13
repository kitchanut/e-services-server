<?php

namespace App\Http\Controllers;

use App\Pole;
use Illuminate\Http\Request;

class PoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $output = Pole::get();
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
        Pole::create($credentials);
        return response()->json([
            'status' => true,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pole  $pole
     * @return \Illuminate\Http\Response
     */
    public function show(Pole $pole)
    {
        return response()->json($pole);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pole  $pole
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pole $pole)
    {
        $credentials = $request->all();
        $pole->update($credentials);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pole  $pole
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pole $pole)
    {
        $pole->delete();
    }
}
