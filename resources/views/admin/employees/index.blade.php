@extends('layouts.app', ['heading' => 'Manajemen Karyawan'])
@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between mb-3"><h2 class="h5">Data Karyawan</h2><a class="btn btn-primary" href="{{ route('admin.employees.create') }}">Tambah Karyawan</a></div>
    <div class="table-responsive"><table class="table align-middle">
        <thead><tr><th>NIK</th><th>Nama</th><th>Role</th><th>Jabatan</th><th>RFID</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse($employees as $employee)
            <tr>
                <td>{{ $employee->employee_id }}</td><td><div class="fw-semibold">{{ $employee->full_name }}</div><div class="small text-muted">{{ $employee->email }}</div></td>
                <td>{{ ucfirst($employee->user->role) }}</td><td>{{ ucfirst($employee->job_role) }}</td><td>{{ $employee->rfidCards->first()->uid ?? '-' }}</td>
                <td>
                    @php($status = $employee->current_status)
                    <span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span>
                </td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.employees.show',$employee) }}">Detail</a>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.employees.edit',$employee) }}">Edit</a>
                    <form class="d-inline" id="delete-employee-{{ $employee->id }}" method="POST" action="{{ route('admin.employees.destroy', $employee) }}">
                        @csrf
                        @method('DELETE')
                        <button
                            class="btn btn-sm btn-outline-danger"
                            type="button"
                            data-confirm-delete
                            data-form-target="#delete-employee-{{ $employee->id }}"
                            data-confirm-title="Hapus {{ $employee->full_name }}?"
                            data-confirm-message="Data karyawan ini akan dihapus dan akun login terkait akan dinonaktifkan."
                        >
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-muted">Belum ada data karyawan.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    {{ $employees->links() }}
</div>
@endsection
