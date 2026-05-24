@extends('layouts.app', ['heading' => 'Edit Karyawan'])
@section('content')
@include('admin.employees._form', ['action' => route('admin.employees.update',$employee), 'method' => 'PUT'])
@endsection
