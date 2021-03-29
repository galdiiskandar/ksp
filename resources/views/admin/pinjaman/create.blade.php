@extends('layouts.template')

@section('title','KSP')
@section('pageName','Pinjaman')

@section('contentHere')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Pinjaman</h3>

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
        <form method="POST" action="{{ route('pinjaman.store') }}">
            @csrf
            <div class="form-group">
                <label for="kode">Kode Pinjaman</label>
                <label class="form-control" style="border: 0; font-weight: normal;">{{$pinjaman}}</label>
                <input type="hidden" class="form-control" id="kode" name="kode" value="{{$pinjaman}}">
            </div>
            <div class="form-group">
                <label for="idAnggota">Nama Anggota</label>
                <select name="idAnggota" class="form-control">
                    <option value="">-- Pilih Satu --</option>
                    @foreach($anggota as $anggota)
                        <option value="{{$anggota->id}}">{{$anggota->id}} / {{$anggota->nama}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal Pinjaman</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal">
            </div>
            <div class="form-group">
                <label for="jaminan">Jaminan</label>
                <input type="text" class="form-control" id="jaminan" name="jaminan" placeholder="Masukkan jaminan">
            </div>
            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="text" class="form-control" id="jumlah" name="jumlah" placeholder="Masukkan jumlah pinjaman">
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <!-- /.card-body -->
</div>
@endsection