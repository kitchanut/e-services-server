<?php

namespace App\Http\Controllers;

use App\Corruption;
use App\Fire;
use App\Narcotic;
use App\Project;
use App\RequestInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function statusType1(Request $request)
    {
        $pedding = Fire::where([['status', 'pedding']])->select('type', DB::raw('count(type) as total'))->groupBy('type')->get();
        $accept = Fire::where([['status', 'accept']])->select('type', DB::raw('count(type) as total'))->groupBy('type')->get();
        $selectTechnician = Fire::where([['status', 'selectTechnician']])->select('type', DB::raw('count(type) as total'))->groupBy('type')->get();
        $sentJob = Fire::where([['status', 'sentJob']])->select('type', DB::raw('count(type) as total'))->groupBy('type')->get();
        $reject = Fire::where([['status', 'reject']])->select('type', DB::raw('count(type) as total'))->groupBy('type')->get();
        $acceptJob = Fire::where([['status', 'acceptJob']])->select('type', DB::raw('count(type) as total'))->groupBy('type')->get();
        $finish = Fire::where([['status', 'finish']])->select('type', DB::raw('count(type) as total'))->groupBy('type')->get();
        $close = Fire::where([['status', 'close']])->select('type', DB::raw('count(type) as total'))->groupBy('type')->get();
        $cancle = Fire::where([['status', 'cancle']])->select('type', DB::raw('count(type) as total'))->groupBy('type')->get();

        $output['pedding'] = $pedding;
        $output['accept'] = $accept;
        $output['selectTechnician'] = $selectTechnician;
        $output['sentJob'] = $sentJob;
        $output['reject'] = $reject;
        $output['acceptJob'] = $acceptJob;
        $output['finish'] = $finish;
        $output['close'] = $close;
        $output['cancle'] = $cancle;


        return response()->json($output);
    }

    public function statusType2(Request $request)
    {
        $pedding['corruption'] = Corruption::where([['status', 'pedding']])->count();
        $accept['corruption'] = Corruption::where([['status', 'accept']])->count();
        $close['corruption'] = Corruption::where([['status', 'close']])->count();
        $cancle['corruption'] = Corruption::where([['status', 'cancle']])->count();

        $pedding['narcotic'] = Narcotic::where([['status', 'pedding']])->count();
        $accept['narcotic'] = Narcotic::where([['status', 'accept']])->count();
        $close['narcotic'] = Narcotic::where([['status', 'close']])->count();
        $cancle['narcotic'] = Narcotic::where([['status', 'cancle']])->count();

        $pedding['requestInformation'] = RequestInformation::where([['status', 'pedding']])->count();
        $accept['requestInformation'] = RequestInformation::where([['status', 'accept']])->count();
        $close['requestInformation'] = RequestInformation::where([['status', 'close']])->count();
        $cancle['requestInformation'] = RequestInformation::where([['status', 'cancle']])->count();

        $pedding['project'] = Project::where([['status', 'pedding']])->count();
        $accept['project'] = Project::where([['status', 'accept']])->count();
        $close['project'] = Project::where([['status', 'close']])->count();
        $cancle['project'] = Project::where([['status', 'cancle']])->count();

        $output['pedding'] = $pedding;
        $output['accept'] = $accept;
        $output['close'] = $close;
        $output['cancle'] = $cancle;

        return response()->json($output);
    }

    public function getLocation(Request $request)
    {
        $timeStart = $request->input('timeStart');
        $timeEnd = $request->input('timeEnd');
        $output = Fire::where('status', '!=', 'cancle')->select('type', 'lat', 'lng', 'number')
            ->where([['created_at', '>=', $timeStart], ['created_at', '<=', $timeEnd]])
            ->get();
        return response()->json($output);
    }
}
