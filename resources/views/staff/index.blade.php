@extends('layouts.app', [
    'title' => 'Staff - Disty Teknologi',
    'pageLabel' => 'Staff'
])

@section('content')
    <div class="staff-page">
        <section class="staff-card">
            <div class="staff-header">
                <div>
                    <h1>Staff Customer Service</h1>
                    <p>Ringkasan jumlah customer yang ditangani oleh masing-masing CS.</p>
                </div>
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

                                <td>
                                    <div class="staff-name">
                                        <div class="staff-avatar">
                                            {{ strtoupper(substr($staff->full_name, 0, 1)) }}
                                        </div>

                                        <div>
                                            <strong>{{ $staff->full_name }}</strong>
                                            <span>{{ $staff->email }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="count-badge neutral">
                                        {{ $staff->total_cust }}
                                    </span>
                                </td>

                                <td>
                                    <span class="count-badge success">
                                        {{ $staff->finished_cust }}
                                    </span>
                                </td>

                                <td>
                                    <span class="count-badge warning">
                                        {{ $staff->ongoing_cust }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-table">
                                    Belum ada data staff customer service.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>@extends('layouts.app', [
    'title' => 'Staff - Disty Teknologi',
    'pageLabel' => 'Staff'
])

@section('content')
    <div class="staff-page">
        <section class="staff-card">
            <div class="staff-header">
                <div>
                    <h1>Staff Customer Service</h1>
                    <p>Ringkasan jumlah customer yang ditangani oleh masing-masing CS.</p>
                </div>
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

                                <td>
                                    <div class="staff-name">
                                        <div class="staff-avatar">
                                            {{ strtoupper(substr($staff->full_name, 0, 1)) }}
                                        </div>

                                        <strong>{{ $staff->full_name }}</strong>
                                    </div>
                                </td>

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
            </div>
        </section>
    </div>
@endsection