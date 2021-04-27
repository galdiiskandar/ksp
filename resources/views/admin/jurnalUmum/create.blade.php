@extends('layouts.template')

@section('title','KSP')
@section('pageName','Jurnal Umum')

@section('contentHere')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Jurnal Umum</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip"
                title="Collapse">
                <i class="fas fa-minus"></i></button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove">
                <i class="fas fa-times"></i></button>
        </div>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger errorAlert">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('jurnal-umum.store') }}">
            @csrf
            <div class="row">
                {{-- <div class="col-xl-12">
                    <div class="form-group col-8">
                        <label for="tanggal">Tanggal Pembayaran</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal[]" onkeydown="return false">
                    </div>
                </div> --}}
                <div class="col-xl-4">
                    <div class="form-group">
                        <label for="tanggal">Tanggal Pembayaran</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal[]" onkeydown="return false">
                    </div>
                    <div class="form-group">
                        <label for="akun">Akun</label>
                        <select class="form-control" name="akun[]">
                            @foreach ($showAkun as $akun)
                                <option value="{{ $akun->noAkun }}"> {{ $akun->noAkun }} - {{ $akun->nama }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="posisi">Posisi</label>
                        <select class="form-control" name="posisi[]">
                            <option value="DEBIT">DEBIT</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah Transaksi</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp </span>
                            </div>
                            <input type="text" name="jumlah[]" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" name="keterangan[]"></textarea>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="form-group">
                        <label for="tanggal">Tanggal Pembayaran</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal[]" onkeydown="return false">
                    </div>
                    <div class="form-group">
                        <label for="akun">Akun</label>
                        <select class="form-control" name="akun[]">
                            @foreach ($showAkun as $akun)
                                <option value="{{ $akun->noAkun }}"> {{ $akun->noAkun }} - {{ $akun->nama }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="posisi">Posisi</label>
                        <select class="form-control" name="posisi[]">
                            <option value="KREDIT">KREDIT</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah Transaksi</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp </span>
                            </div>
                            <input type="text" name="jumlah[]" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" name="keterangan[]"></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <!-- /.card-body -->
</div>
@endsection

@section('scriptPlace')
@endsection
