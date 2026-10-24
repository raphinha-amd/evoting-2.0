@extends('layouts.export')
@section('title', 'Export Data User')

@section('table')
    <table>
        <thead>
            <tr>
                <th width="40">No</th>
                <th>Nama Lengkap</th>
                <th>NIM / Username</th>
                <th>Password</th>
                <th>Role</th>
                <th>Kelas</th>
                <th>Program Studi</th>
            </tr>
        </thead>
        <tbody>
            @if ($users->isEmpty())
                <tr>
                    <td colspan="7" style="text-align: center;">Data User Belum Tersedia</td>
                </tr>
            @else
                @foreach ($users as $item)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $item->fullname }}</td>
                        <td>{{ $item->username }}</td>
                        <td>{{ $item->unencrypted_password }}</td>
                        <td style="text-transform: capitalize;">{{ $item->roleData ? $item->roleData->name : $item->role }}</td>
                        <td>{{ $item->kelas->name ?? '-' }}</td>
                        <td>{{ $item->kelas && $item->kelas->prodi ? $item->kelas->prodi->name : '-' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
@endsection
