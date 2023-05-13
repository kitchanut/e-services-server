<?php

use App\Http\Controllers\CorruptionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\FireController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobEquipmentController;
use App\Http\Controllers\JobResultController;
use App\Http\Controllers\LinebotController;
use App\Http\Controllers\LineBotControllers;
use App\Http\Controllers\NarcoticController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PoleController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequestInformationController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserLineController;
use App\Http\Controllers\UserTechnicianController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\User;
use App\UserLine;
use App\userPermission;
use App\UserTechnician;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('login', function () {
    abort(401);
})->name('login');

// Route::post('lineBot', [LineBotControllers::class, 'store']);
Route::apiResource('lineBot', LinebotController::class);

Route::get('checkRegister/{lineUUID}', function ($lineUUID) {
    $user = User::where('email', $lineUUID)->first();
    if ($user) {
        return response()->json(['status' => true, 'user' => $user]);
    } else {
        return response()->json(['status' => false]);
    }
});

Route::get('checkTechnicianRegister/{lineUUID}', function ($lineUUID) {
    $user = User::where('email', $lineUUID)->first();
    if ($user) {
        return response()->json(['status' => true, 'user' => $user]);
    } else {
        return response()->json(['status' => false]);
    }
});

Route::get('checkCustomerRegister/{lineUUID}', function ($lineUUID) {
});

Route::post('login', function () {
    $tokenName = request()->only(['tokenName']);
    if ($tokenName['tokenName'] == 'Liff') {
        $credentials = request()->only(['lineUUID']);
        $user = User::where('email',  $credentials['lineUUID'])->first();
        if (Auth::loginUsingId($user->id)) {
            $user->tokens()->where('name', 'Liff')->delete();
            // $user->tokens()->delete();
            $token = $user->createToken('Liff', [request()->abilities]);
            $res = explode("|", $token->plainTextToken);
            return response()->json(['status' => true, 'token' => $res[1], 'user' => $user]);
        } else {
            return response()->json(['status' => false], 401);
        }
    } else {
        $credentials = request()->only(['email', 'password']);
        if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'user_isActive' => 'เปิดใช้งาน'])) {
            return response()->json(['status' => false], 401);
        } else {

            $user = User::where('email', $credentials['email'])->first();
            $user->tokens()->where('name', 'Web')->delete();
            // $user->tokens()->delete();
            $token = $user->createToken('Web', [$user['level']]);
            $res = explode("|", $token->plainTextToken);

            $permission = userPermission::where('user_id', $user->id)->get();
            return response()->json(['status' => true, 'token' => $res[1], 'user' => $user, 'permission' => $permission], 200);
        }
    }
});

Route::get('users/getTechnician', [UserController::class, 'getTechnician']);
Route::apiResource('users', UserController::class);

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::delete('logout', function (Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['status' => true]);
    });

    Route::get('me', function (Request $request) {
        $request->user();
        return response()->json(['user' => $request->user()]);
    });





    Route::apiResource('user_lines', UserLineController::class);
    Route::apiResource('user_technicians', UserTechnicianController::class);
    Route::apiResource('uploads', UploadController::class);
    Route::apiResource('equipment', EquipmentController::class);

    Route::get('pages/eachUser/{user_id}', [PageController::class, 'eachUser']);
    Route::apiResource('pages', PageController::class);

    Route::apiResource('poles', PoleController::class);

    Route::post('fires/indexCustom', [FireController::class, 'indexCustom']);
    Route::post('fires/countData/{type}', [FireController::class, 'countData']);
    Route::apiResource('fires', FireController::class);

    Route::get('timelines/showDetails/{type}/{id}', [TimelineController::class, 'showDetails']);
    Route::get('timelines/accept/{type}/{id}', [TimelineController::class, 'accept']);
    Route::get('timelines/sentJob/{type}/{id}', [TimelineController::class, 'sentJob']);
    Route::get('timelines/close/{type}/{id}', [TimelineController::class, 'close']);
    Route::post('timelines/cancleItem/{type}/{id}', [TimelineController::class, 'cancleItem']);
    Route::apiResource('timelines', TimelineController::class);

    Route::get('jobs/checkJob/{type}/{id}', [JobController::class, 'checkJob']);
    Route::get('jobs/getByTechnician/{user_technician_id}', [JobController::class, 'getByTechnician']);
    Route::post('jobs/cancleItem/{type}/{id}', [JobController::class, 'cancleItem']);
    Route::post('jobs/action/{type}/{id}/{actionType}', [JobController::class, 'action']);
    Route::apiResource('jobs', JobController::class);

    Route::get('job_results/check/{type}/{ref_id}', [JobResultController::class, 'check']);
    Route::apiResource('job_results', JobResultController::class);

    Route::get('job_equipment/getByJobId/{job_id}', [JobEquipmentController::class, 'getByJobId']);
    Route::apiResource('job_equipment', JobEquipmentController::class);

    Route::post('corruptions/indexCustom', [CorruptionController::class, 'indexCustom']);
    Route::post('corruptions/countData', [CorruptionController::class, 'countData']);
    Route::apiResource('corruptions', CorruptionController::class);

    Route::post('narcotics/indexCustom', [NarcoticController::class, 'indexCustom']);
    Route::post('narcotics/countData', [NarcoticController::class, 'countData']);
    Route::apiResource('narcotics', NarcoticController::class);

    Route::post('request_information/indexCustom', [RequestInformationController::class, 'indexCustom']);
    Route::post('request_information/countData', [RequestInformationController::class, 'countData']);
    Route::apiResource('request_information', RequestInformationController::class);

    Route::post('projects/indexCustom', [ProjectController::class, 'indexCustom']);
    Route::post('projects/countData', [ProjectController::class, 'countData']);
    Route::apiResource('projects', ProjectController::class);

    Route::post('report/equipment', [ReportController::class, 'equipment']);

    Route::get('dashboard/statusType1', [DashboardController::class, 'statusType1']);
    Route::get('dashboard/statusType2', [DashboardController::class, 'statusType2']);
    Route::post('dashboard/getLocation', [DashboardController::class, 'getLocation']);
});

// Route::apiResource('users', 'UserController');

Route::prefix('liff')->group(function () {
});
