<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\OfficeTrip;
use App\Models\OvertimeRequest;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $bawahanIds = $request->user()->bawahan()->pluck('id');

        $izin = LeaveRequest::whereIn('user_id', $bawahanIds)
            ->where('status', 'pending')->with('user')->latest()->get();

        $lembur = OvertimeRequest::whereIn('user_id', $bawahanIds)
            ->where('status', 'pending')->with('user')->latest()->get();

        $dinas = OfficeTrip::whereIn('user_id', $bawahanIds)
            ->where('status', 'pending')->with('user')->latest()->get();

        return view('approval.index', [
            'izin' => $izin,
            'lembur' => $lembur,
            'dinas' => $dinas,
        ]);
    }
}
