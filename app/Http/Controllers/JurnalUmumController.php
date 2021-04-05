<?php

namespace App\Http\Controllers;

use App\Models\JurnalUmum;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Helpers\Helper;

class JurnalUmumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $showJurnal = DB::table('jurnal_umum')
            ->join('akun','jurnal_umum.noAkun','=','akun.noAkun')
            ->select(
                    'jurnal_umum.noTransaksi',
                    'jurnal_umum.tanggal',
                    'jurnal_umum.jumlah',
                    'jurnal_umum.keterangan',
                    'jurnal_umum.status',
                    'akun.nama as namaAkun'
                    )
            ->get();

        return view('admin.jurnalUmum/index',[
            'showJurnal' => $showJurnal
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $showAkun = DB::table('akun')
                    ->get();

        return view('admin.jurnalUmum/create',[
            'showAkun' => $showAkun
        ]);
    }
    /*
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = array(
            'tanggal.required' => 'Tanggal simpanan tidak boleh kosong!',
            'jumlah.required' => 'Jumlah simpanan tidak boleh kosong!',
            'jumlah.numeric' => 'Harap memasukkan Angka!',
            'keterangan.required' => 'Keterangan tidak boleh kosong!',
        );

        $validate = $request->validate([
            'tanggal' => 'required',
            'jumlah' => 'numeric|required',
            'keterangan'=> 'required'
        ],$messages);

        //custom number generator
        $lastNo = JurnalUmum::select('noTransaksi')->orderByDesc('noTransaksi')->first();
        $lastNo=(int)substr($lastNo , -5);
        $newgeneratedNo = "JU-".str_pad($lastNo+1, 5, "0", STR_PAD_LEFT);

        if($validate){
            $data = [
                'noTransaksi' => $newgeneratedNo,
                'noAkun' => $request->akun,
                'tanggal' => $request->tanggal,
                'jumlah' => $request->jumlah,
                'status' => $request->posisi,
                'keterangan' => $request->keterangan,
                'idAdmin' => auth()->user()->id
            ];

            $insertJurnal = JurnalUmum::create($data);

            if($insertJurnal){
                return redirect('admin/jurnal-umum')->with('success','Data Berhasil Disimpan');
            }else{
                return redirect('admin/jurnal-umum.create')->with('error','Data Gagal Disimpan');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\JurnalUmum  $jurnalUmum
     * @return \Illuminate\Http\Response
     */
    public function show(JurnalUmum $jurnalUmum)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\JurnalUmum  $jurnalUmum
     * @return \Illuminate\Http\Response
     */
    public function edit(JurnalUmum $jurnalUmum)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JurnalUmum  $jurnalUmum
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JurnalUmum $jurnalUmum)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JurnalUmum  $jurnalUmum
     * @return \Illuminate\Http\Response
     */
    public function destroy(JurnalUmum $jurnalUmum)
    {
        //
    }
}
