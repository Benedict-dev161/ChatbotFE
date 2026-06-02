@extends('layouts.app', [
    'title' => 'Staff - Disty Teknologi',
    'pageLabel' => 'Staff'
])

@section('content')
    <div class="staff-page">
        <section class="staff-card">
            <div class="staff-header">
                <h1>Staff Customer Service</h1>
                <p>Ringkasan jumlah customer yang ditangani oleh masing-masing CS.</p>
            </div>

            <div class="staff-table-wrapper">
                <table class="staff-table">
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Nama</th>
                            <th>Total Cust</th>
                            <th>Finished Cust</th>
                            <th>On-going Cust</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($staffStats as $index => $staff)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $staff->full_name }}</td>
                                <td>{{ $staff->total_cust }}</td>
                                <td>{{ $staff->finished_cust }}</td>
                                <td>{{ $staff->ongoing_cust }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-table">
                                    Belum ada data staff customer service.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection