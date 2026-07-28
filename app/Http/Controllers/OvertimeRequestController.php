<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class OvertimeRequestController extends Controller
{
    public function index(Request $request){
        $overtimeRequests = $request->user()->overtimeRequests()->latest()->get();

        return Inertia::render('overtime/index', [
            'overtimeRequests' => $overtimeRequests
        ]);
    }

    public function create(){
        return Inertia::render('overtime/create');
    }

    public function store(Request $request){
         $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'lokasi_lembur' => 'required|string|max:255',
            'alasan' => 'required|string',
        ], [
            'tanggal.required' => 'Tanggal lembur wajib dipilih.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'lokasi_lembur.required' => 'Lokasi lembur wajib diisi.',
            'alasan.required' => 'Alasan lembur wajib diisi.',
        ]);


        $request->user()->overtimeRequests()->create($request->only([
            'tanggal', 'jam_mulai', 'jam_selesai', 'lokasi_lembur', 'alasan'
        ]));

        return redirect()->route('lembur.index')->with('success', 'Pengajuan lembur berhasil dikirim.');
    }
}
