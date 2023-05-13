<?php

namespace App\Http\Controllers;

use App\Page;
use App\userPermission;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $output = Page::orderBy('order')
            ->get();
        return response()->json($output);
    }

    public function eachUser($user_id)
    {
        $output = Page::where('page_status', 'เปิดใช้งาน')
            ->orderBy('order')
            ->get();
        $output->map(function ($item) use ($user_id) {
            $permission = userPermission::where([['user_id', $user_id], ['page_id', $item->id]])->first();
            if ($permission) {
                $item['permission_read'] = $permission->permission_read;
                $item['permission_write'] = $permission->permission_write;
                $item['permission_update'] = $permission->permission_update;
                $item['permission_delete'] = $permission->permission_delete;
            } else {
                $item['permission_read'] = 0;
                $item['permission_write'] = 0;
                $item['permission_update'] = 0;
                $item['permission_delete'] = 0;
            }
        });
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
        Page::create($credentials);
        return response()->json([
            'status' => true,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page)
    {
        return response()->json($page);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {
        $credentials = $request->all();
        $page->update($credentials);
        return response()->json([
            'status' => true,
            'data' => $credentials
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        return response()->json($page->delete());
    }
}
