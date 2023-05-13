<?php

namespace App\Http\Controllers;

use App\JobEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function equipment(Request $request)
    {
        $timeStart = $request->input('timeStart');
        $timeEnd = $request->input('timeEnd');
        $output = JobEquipment::with('equipment')
            ->where([['created_at', '>=', $timeStart], ['created_at', '<=', $timeEnd]])
            ->select('equipment_id', DB::raw('sum(quantity) as total'))
            ->groupBy('equipment_id')
            ->get();
        return response()->json($output);
    }
}
