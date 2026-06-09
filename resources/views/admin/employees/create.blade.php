@extends('layouts.app', ['heading' => 'Tambah Karyawan'])
@section('content')
@include('admin.employees._form', ['action' => route('admin.karyawan.store'), 'method' => 'POST', 'employee' => null])
@endsection
