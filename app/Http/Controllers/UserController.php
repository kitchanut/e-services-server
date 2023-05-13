<?php

namespace App\Http\Controllers;

use App\User;
use App\userPermission;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $output = User::get();
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
        $credentials = $request->except('permissions');


        $credentials['password'] = bcrypt($credentials['password']);
        $user = User::where('email', $credentials['email'])->first();
        if ($user) {
            return response()->json([
                'status' => false,
            ], 200);
        } else {
            $user = User::create($credentials);

            $permissions = $request->only('permissions');
            if (isset($permissions['permissions'])) {
                foreach ($permissions['permissions'] as $key => $value) {
                    $user_permission = userPermission::where([['user_id', $user['id']], ['page_id', $value['id']]])->first();
                    if ($user_permission) {
                        $user_permission['page_code'] = $value['page_code'];
                        $user_permission['permission_read'] = $value['permission_read'];
                        $user_permission['permission_write'] = $value['permission_write'];
                        $user_permission['permission_update'] = $value['permission_update'];
                        $user_permission['permission_delete'] = $value['permission_delete'];
                        $user_permission->save();
                    } else {
                        $InsertPermission['user_id'] = $user['id'];
                        $InsertPermission['page_id'] = $value['id'];
                        $InsertPermission['page_code'] = $value['page_code'];
                        $InsertPermission['permission_read'] = $value['permission_read'];
                        $InsertPermission['permission_write'] = $value['permission_write'];
                        $InsertPermission['permission_update'] = $value['permission_update'];
                        $InsertPermission['permission_delete'] = $value['permission_delete'];
                        userPermission::create($InsertPermission);
                    }
                }
            }


            return response()->json([
                'status' => true,
            ], 201);
        }
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $credentials = $request->except('permissions');
        if (isset($credentials['password'])) {
            $credentials['password'] = bcrypt($credentials['password']);
        } else {
            unset($credentials['password']);
        }

        if ($user->level == 'user_line' || $user->level == 'technician') {
            $user->update($credentials);
            return response()->json([
                'status' => true,
                'data' => $credentials
            ], 200);
        }

        $userCheck = User::where([['id', '!=', $credentials['id']], ['email', $credentials['email']]])->first();
        if ($userCheck) {
            return response()->json([
                'status' => false,
            ], 200);
        } else {
            $user->update($credentials);

            $permissions = $request->only('permissions');
            if (isset($permissions['permissions'])) {
                foreach ($permissions['permissions'] as $key => $value) {
                    $user_permission = userPermission::where([['user_id', $user['id']], ['page_id', $value['id']]])->first();
                    if ($user_permission) {
                        $user_permission['page_code'] = $value['page_code'];
                        $user_permission['permission_read'] = $value['permission_read'];
                        $user_permission['permission_write'] = $value['permission_write'];
                        $user_permission['permission_update'] = $value['permission_update'];
                        $user_permission['permission_delete'] = $value['permission_delete'];
                        $user_permission->save();
                    } else {
                        $InsertPermission['user_id'] = $user['id'];
                        $InsertPermission['page_id'] = $value['id'];
                        $InsertPermission['page_code'] = $value['page_code'];
                        $InsertPermission['permission_read'] = $value['permission_read'];
                        $InsertPermission['permission_write'] = $value['permission_write'];
                        $InsertPermission['permission_update'] = $value['permission_update'];
                        $InsertPermission['permission_delete'] = $value['permission_delete'];
                        userPermission::create($InsertPermission);
                    }
                }
            }

            return response()->json([
                'status' => true,
                'data' => $credentials
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // $user->user_isActive = 'ลบ';
        // $user->save();
        userPermission::where('user_id', $user->id)->delete();
        $user->delete();
    }

    public function getTechnician()
    {
        $output = User::where([['level', 'technician'], ['user_isActive', 'เปิดใช้งาน']])->get();
        return response()->json($output);
    }
}
