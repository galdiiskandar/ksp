<?php

namespace App\Http\Controllers;

use App\Models\SimpananKhusus;
use Illuminate\Http\Request;
use DB;

class SimpananKhususController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dataSimpananKhusus = DB::table('simpanan_khusus')
                                ->join('anggota', 'simpanan_khusus.idAnggota', '=', 'anggota.id')
                                ->select('simpanan_khusus.*','anggota.nama as namaAnggota','anggota.id as idAnggota')
                                ->get();

        return view('admin/simpananKhusus.index',[
            'simpananKhusus' => $dataSimpananKhusus
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $selectAnggota = DB::table('anggota')
                            ->select('id','nama')
                            ->get();

        $code = 'SPS';
        $last = DB::table('simpanan_khusus')
                ->where('kode', 'like', '%'.$code.'%')
                ->max('kode');

        if($last == null)
        {
            $kodeSimpananKhusus = $code.'001';
        } else {
            $new = substr($last,-3);
            $new +=1;
            $kodeSimpananKhusus = $code.sprintf("%03d", $new);
        }

        return view('admin/simpananKhusus.create',[
            'anggota' => $selectAnggota,
            'simpananKhusus' => $kodeSimpananKhusus
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $lastSimpananKhusus = DB::table('simpanan_khusus')
                        ->select('kode','idAnggota','saldo')
                        ->where('idAnggota', $request->idAnggota)
                        ->orderBy('kode','desc')
                        ->first();

        if($lastSimpananKhusus != null){
            $Totalsaldo = $lastSimpananKhusus->saldo + $request->jumlah;
        }else{
            $Totalsaldo = $request->jumlah;
        }

        $messages = array(
            'tanggal.required' => 'Tanggal simpanan tidak boleh kosong!',
            'idAnggota.required' => 'Nama anggota tidak boleh kosong!',
            'jumlah.required' => 'Jumlah simpanan tidak boleh kosong!'
        );

        $validate = $request->validate([
            'tanggal' => 'required',
            'idAnggota'=> 'required',
            'jumlah' => 'required'
        ],$messages);

        $data = [
            'kode'=> $request->kode,
            'idAnggota' => $request->idAnggota,
            'tanggal' => $request->tanggal,
            'jumlah' => $request->jumlah,
            'bunga' => $request->bunga,
            'saldo' => $Totalsaldo
        ];

        $insertData = SimpananKhusus::create($data);

        if($insertData){
            return redirect('admin/simpananKhusus')->with('success','Data Berhasil Disimpan');
        }else{
            return redirect('admin/simpananKhusus.create')->with('error','Data Gagal Disimpan');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SimpananKhusus  $simpananKhusus
     * @return \Illuminate\Http\Response
     */
    public function show(SimpananKhusus $simpananKhusus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SimpananKhusus  $simpananKhusus
     * @return \Illuminate\Http\Response
     */
    public function edit(SimpananKhusus $simpananKhusus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SimpananKhusus  $simpananKhusus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SimpananKhusus $simpananKhusus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SimpananKhusus  $simpananKhusus
     * @return \Illuminate\Http\Response
     */
    public function destroy(SimpananKhusus $simpananKhusus)
    {
        //
    }
}