<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index(Request $request){
        $bawahanIds = $request->user()->bawahan()->pluck('id');

          $izin = \App\Models\LeaveRequest::whereIn('user_id', $bawahanIds)
            ->where('status', 'pending')->with('user')->latest()->get();

            $lembur = \App\Models\OvertimeRequest::whereIn('user_id', $bawahanIds)
                ->where('status', 'pending')->with('user')->latest()->get();

            $dinas = \App\Models\OfficeTrip::whereIn('user_id', $bawahanIds)
                ->where('status', 'pending')->with('user')->latest()->get();

            return Inertia::render('approval/index', [
                'izin' => $izin,
                'lembur' => $lembur,
                'dinas' => $dinas,
            ]);
    }
}
