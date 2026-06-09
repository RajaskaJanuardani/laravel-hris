@extends('layouts.app', ['heading' => 'Edit Karyawan'])
@section('content')
@include('admin.employees._form', ['action' => route('admin.karyawan.update',$employee), 'method' => 'PUT'])
@endsection
