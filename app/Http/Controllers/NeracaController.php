<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class NeracaController extends Controller
{
    public function index(){
        $mytime = Carbon::now();

        $convertedDate = $mytime->toDateString();

        return view('admin/neraca.index',[
            'dariTanggal' => $convertedDate,
            'sampaiTanggal' => $convertedDate,
        ]);
    }

    public function getNeraca(Request $request){

        $dariTanggal = $request->dariTanggal;
        $sampaiTanggal = $request->sampaiTanggal;

        $noAkun = [];
        $noAkun_search = DB::table('akun')->orderBy('noAkun', 'asc')->get();

        foreach ($noAkun_search as $key1 => $ns) {

            $filterParent = DB::table('jurnal_umum')
            ->join('akun','jurnal_umum.noAkun','=','akun.noAkun')
            ->select(
                'jurnal_umum.id as IDJurnal',
                'jurnal_umum.noTransaksi',
                'jurnal_umum.tanggal',
                'jurnal_umum.status',
                'jurnal_umum.keterangan',
                'jurnal_umum.noAkun',
                'akun.nama as namaAkun',
                'akun.tipe as tipeAkun',
            )
            ->selectRaw('cast(sum(jurnal_umum.jumlah)as UNSIGNED) as testJumlah')
            ->where('jurnal_umum.noAkun', $ns->noAkun)
            ->whereBetween('jurnal_umum.tanggal',[$request->dariTanggal,$request->sampaiTanggal])
            ->get();

            $filterParent2 = DB::table('jurnal_umum')
            ->join('akun','jurnal_umum.noAkun','=','akun.noAkun')
            ->select(
                'jurnal_umum.id as IDJurnal',
                'jurnal_umum.noTransaksi',
                'jurnal_umum.tanggal',
                'jurnal_umum.status',
                'jurnal_umum.keterangan',
                'jurnal_umum.noAkun',
                'akun.nama as namaAkun',
                'akun.tipe as tipeAkun',
            )
            ->selectRaw('cast(sum(jurnal_umum.jumlah)as UNSIGNED) as testJumlah')
            ->where('jurnal_umum.status', 'KREDIT')
            ->where('jurnal_umum.noAkun', $ns->noAkun)
            ->whereBetween('jurnal_umum.tanggal',[$request->dariTanggal,$request->sampaiTanggal])
            ->get();

            if (@$filterParent[0]->testJumlah != null ) {
                $noAkun[$key1]['noAkun'] = $ns->noAkun;
                $noAkun[$key1]['hasilAkhir'] = $filterParent[0]->testJumlah - $filterParent2[0]->testJumlah;
                $noAkun[$key1]['tipeAkun'] = $filterParent[0]->tipeAkun;
                $noAkun[$key1]['namaAkun'] = $filterParent[0]->namaAkun;
                $noAkun[$key1]['statusAkun'] = $filterParent[0]->status;
                if(@$filterParent[0]->tipeAkun == "Aktiva Tetap"){
                    $noAkun[$key1]['statusAkun'] = 'KREDIT';
                }
                if(@$filterParent[0]->namaAkun == "Kas"){
                    $noAkun[$key1]['statusAkun'] = 'DEBIT';
                }
            }
            if (@$filterParent2[0]->tipeAkun == 'Pendapatan') {
                $noAkun[$key1]['noAkun'] = $ns->noAkun;
                $noAkun[$key1]['hasilAkhir'] = $filterParent[0]->testJumlah;
                $noAkun[$key1]['tipeAkun'] = $filterParent[0]->tipeAkun;
                $noAkun[$key1]['namaAkun'] = $filterParent[0]->namaAkun;
                $noAkun[$key1]['statusAkun'] = 'KREDIT';
            }
        }

        $dataSimpanan = DB::select('
            SELECT
                anggota.id AS idAnggota,
                anggota.nama AS namaAnggota,
                simpanan.kode,
                simpanan.tanggal,
                detail_simpanan.saldo as saldo
            FROM detail_simpanan
            JOIN simpanan ON simpanan.kode = detail_simpanan.kodeSimpanan
            JOIN anggota ON simpanan.idAnggota = anggota.id
            WHERE detail_simpanan.kode IN
            (SELECT MAX(detail_simpanan.kode) FROM detail_simpanan GROUP BY detail_simpanan.kodeSimpanan)
        ');

        $totalSimpanan = 0;

        foreach ($dataSimpanan as $key1 => $ds) {
            $totalSimpanan += $ds->saldo;
        }

        $count = count($noAkun);
        $noAkun[$count]['noAkun'] = 1111;
        $noAkun[$count]['hasilAkhir'] = $totalSimpanan;
        $noAkun[$count]['tipeAkun'] = 'Kewajiban';
        $noAkun[$count]['namaAkun'] = 'Simpanan Harian';
        $noAkun[$count]['statusAkun'] = 'DEBIT';

        $modalSendiri = [];

        $simpananPokok = DB::table('simpanan_pokok')
        ->sum('jumlah');

        $simpananWajib = DB::table('simpanan_wajib')
        ->sum('jumlah');

        $simpananKhusus = DB::table('simpanan_khusus')
        ->sum('saldo');

        $pinjaman = DB::table('pinjaman')
        ->whereBetween('tanggal',[$request->dariTanggal,$request->sampaiTanggal])
        ->sum('jumlah');

        for($x=0;$x<3;$x++){
                if($x==0 && $simpananPokok != null){
                    $modalSendiri[$x]['namaAkun'] = 'Simpanan Pokok';
                    $modalSendiri[$x]['jumlah'] = $simpananPokok;
                }else if($x==1 && $simpananWajib != null){
                    $modalSendiri[$x]['namaAkun'] = 'Simpanan Wajib';
                    $modalSendiri[$x]['jumlah'] = $simpananWajib;
                }else{
                    if($simpananKhusus != null){
                        $modalSendiri[$x]['namaAkun'] = 'Simpanan Khusus';
                        $modalSendiri[$x]['jumlah'] = $simpananKhusus;
                    }
                }
        }

        $totalSimpanan = $totalSimpanan + $simpananPokok + $simpananWajib + $simpananKhusus;

        $count = count($noAkun);

        $key = 'Kas';
        $result = collect($noAkun)->contains('namaAkun', 'Kas');

        if ($result == true){
            for($x=0;$x<$count;$x++){
                if($noAkun[$x]['namaAkun'] == 'Kas'){
                    $total = $noAkun[$x]['hasilAkhir'];
                    $totalAkhir = $total + $totalSimpanan;
                    $noAkun[$x]['hasilAkhir'] = $totalAkhir;
                }
            }
            $res = 'masuk sini';
        } else {
            $noAkun[$count]['noAkun'] = 111;
            $noAkun[$count]['hasilAkhir'] = $totalSimpanan;
            $noAkun[$count]['tipeAkun'] = 'Aktiva Lancar';
            $noAkun[$count]['namaAkun'] = 'Kas';
            $noAkun[$count]['statusAkun'] = 'DEBIT';
            $res = 'masuk sina';
        }

        $count = count($noAkun);

        if($pinjaman != 0){
            $noAkun[$count]['noAkun'] = 11111;
            $noAkun[$count]['hasilAkhir'] = $pinjaman;
            $noAkun[$count]['tipeAkun'] = 'Aktiva Lancar';
            $noAkun[$count]['namaAkun'] = 'Pinjaman Anggota';
            $noAkun[$count]['statusAkun'] = 'KREDIT';
        }

        $noAkun = array_values($noAkun);

        return view('admin/neraca.index',[
            'akun' => $noAkun,
            'modalSendiri' => $modalSendiri,
            'dariTanggal' => $dariTanggal,
            'sampaiTanggal' => $sampaiTanggal,
        ]);
    }

    public function indexPercobaan(){
        $mytime = Carbon::now();

        $convertedDate = $mytime->toDateString();

        return view('admin/neraca.indexPercobaan',[
            'dariTanggal' => $convertedDate
        ]);
    }

    public function getNeracaPercobaan(Request $request){

        $bulan = Carbon::parse($request->dariTanggal)->format('m');
        $dariTanggal = $request->sampaiTanggal;

        $noAkun = [];
        $noAkun_search = DB::table('akun')->orderBy('noAkun', 'asc')->get();

        foreach ($noAkun_search as $key1 => $ns) {
            $noAkun[$key1]['noAkun'] = $ns->noAkun;
            $filterParent = DB::table('jurnal_umum')
            ->join('akun','jurnal_umum.noAkun','=','akun.noAkun')
            ->select(
                'jurnal_umum.id as IDJurnal',
                'jurnal_umum.noTransaksi',
                'jurnal_umum.tanggal',
                'jurnal_umum.status',
                'jurnal_umum.keterangan',
                'jurnal_umum.noAkun',
                'akun.nama as namaAkun',
                'akun.tipe as tipeAkun',
            )
            ->selectRaw('cast(sum(jurnal_umum.jumlah)as UNSIGNED) as testJumlah')
            ->where('jurnal_umum.noAkun', $ns->noAkun)
            ->whereMonth('jurnal_umum.tanggal', '<', $bulan)
            ->get();

            if (@$filterParent[0]->testJumlah != null ) {
                $noAkun[$key1]['hasilAkhir'] = $filterParent[0]->testJumlah + $ns->saldo;
                $noAkun[$key1]['tipeAkun'] = $filterParent[0]->tipeAkun;
                $noAkun[$key1]['namaAkun'] = $filterParent[0]->namaAkun;
                if(@$filterParent[0]->tipeAkun == 'Aktiva Lancar' || @$filterParent[0]->tipeAkun == 'Aktiva Tetap' || @$filterParent[0]->tipeAkun == 'Harta Tak Berwujud'){
                    $noAkun[$key1]['status'] = 'DEBIT';
                }else{
                    $noAkun[$key1]['status'] = 'KREDIT';
                }
            } else {
                $noAkun[$key1]['hasilAkhir'] = 0 + $ns->saldo;
                $noAkun[$key1]['tipeAkun'] = $filterParent[0]->tipeAkun;
                $noAkun[$key1]['namaAkun'] = $filterParent[0]->namaAkun;
                if(@$filterParent[0]->tipeAkun == 'Aktiva Lancar' || @$filterParent[0]->tipeAkun == 'Aktiva Tetap' || @$filterParent[0]->tipeAkun == 'Harta Tak Berwujud'){
                    $noAkun[$key1]['status'] = 'DEBIT';
                }else{
                    $noAkun[$key1]['status'] = 'KREDIT';
                }
            }
        }

        $noAkunNow = [];
        $noAkun_search = DB::table('akun')->orderBy('noAkun', 'asc')->get();

        foreach ($noAkun_search as $key1 => $ns) {
            $noAkunNow[$key1]['noAkun'] = $ns->noAkun;
            $filterParent = DB::table('jurnal_umum')
            ->join('akun','jurnal_umum.noAkun','=','akun.noAkun')
            ->select(
                'jurnal_umum.id as IDJurnal',
                'jurnal_umum.noTransaksi',
                'jurnal_umum.tanggal',
                'jurnal_umum.status',
                'jurnal_umum.keterangan',
                'jurnal_umum.noAkun',
                'akun.nama as namaAkun',
                'akun.tipe as tipeAkun',
            )
            ->selectRaw('cast(sum(jurnal_umum.jumlah)as UNSIGNED) as testJumlah')
            ->where('jurnal_umum.noAkun', $ns->noAkun)
            ->whereMonth('jurnal_umum.tanggal', $bulan)
            ->get();

            if (@$filterParent[0]->testJumlah != null ) {
                $noAkunNow[$key1]['hasilAkhir'] = $filterParent[0]->testJumlah;
                $noAkunNow[$key1]['tipeAkun'] = $filterParent[0]->tipeAkun;
                $noAkunNow[$key1]['namaAkun'] = $filterParent[0]->namaAkun;
                if(@$filterParent[0]->tipeAkun == 'Aktiva Lancar' || @$filterParent[0]->tipeAkun == 'Aktiva Tetap' || @$filterParent[0]->tipeAkun == 'Harta Tak Berwujud'){
                    $noAkunNow[$key1]['status'] = 'DEBIT';
                }else{
                    $noAkunNow[$key1]['status'] = 'KREDIT';
                }
            } else {
                $noAkunNow[$key1]['hasilAkhir'] = 0;
                $noAkunNow[$key1]['tipeAkun'] = $filterParent[0]->tipeAkun;
                $noAkunNow[$key1]['namaAkun'] = $filterParent[0]->namaAkun;
                if(@$filterParent[0]->tipeAkun == 'Aktiva Lancar' || @$filterParent[0]->tipeAkun == 'Aktiva Tetap' || @$filterParent[0]->tipeAkun == 'Harta Tak Berwujud'){
                    $noAkunNow[$key1]['status'] = 'DEBIT';
                }else{
                    $noAkunNow[$key1]['status'] = 'KREDIT';
                }
            }
        }

        return view('admin/neraca.indexPercobaan',[
            'akun' => $noAkun,
            'akunNow' => $noAkunNow,
            'dariTanggal' => $dariTanggal,
        ]);
    }
}

