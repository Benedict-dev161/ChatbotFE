@extends('layouts.app', [
    'title' => 'Dashboard - Disty Teknologi',
    'pageLabel' => 'Dashboard'
])

@section('content')

<style>
    .dashboard-page {
        padding: 28px;
        background: #f7f4ed;
        min-height: calc(100vh - 68px);
    }

    .dashboard-top-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 22px;
    }

    .dashboard-middle-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 22px;
    }

    .dashboard-card {
        background: #ffffff;
        border: 1px solid #e3dbcf;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(74, 48, 24, 0.08);
        padding: 24px;
    }

    .card-title {
        margin: 0 0 18px;
        color: #8a6f5f;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    .monthly-card {
        min-height: 230px;
    }

    .monthly-chart {
        height: 110px;
        display: flex;
        align-items: flex-end;
        gap: 10px;
        margin-top: 8px;
    }

    .chart-column {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }

    .chart-bar {
        width: 26px;
        border-radius: 5px 5px 0 0;
        background: #c09a73;
    }

    .chart-bar.is-active {
        background: #c47a35;
    }

    .chart-column span {
        color: #8a6f5f;
        font-size: 11px;
    }

    .monthly-info {
        margin-top: 14px;
    }

    .monthly-label-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .monthly-label-row strong {
        color: #1f1b18;
        font-size: 14px;
        font-weight: 700;
    }

    .growth-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .growth-badge.positive {
        color: #15803d;
        background: #dcfce7;
    }

    .growth-badge.negative {
        color: #dc2626;
        background: #fee2e2;
    }

    .monthly-number {
        margin-top: 4px;
        color: #6f4a2f;
        font-size: 28px;
        font-weight: 800;
    }

    .unanswered-card {
        min-height: 230px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .unanswered-number {
        color: #e5484d;
        font-size: 58px;
        font-weight: 800;
        line-height: 1;
    }

    .unanswered-card p {
        margin: 14px 0 0;
        color: #6f4a2f;
        font-size: 16px;
    }

    .metric-card {
        min-height: 145px;
    }

    .metric-number {
        color: #111111;
        font-size: 46px;
        font-weight: 800;
        letter-spacing: 2px;
        line-height: 1.1;
    }

    .metric-number.danger {
        color: #e5484d;
    }

    .metric-number.response-time {
        font-size: 40px;
        letter-spacing: 1px;
    }

    .metric-card p {
        margin: 8px 0 0;
        color: #8a6f5f;
        font-size: 14px;
    }

    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .activity-row {
        display: grid;
        grid-template-columns: 12px 1fr auto;
        align-items: center;
        gap: 12px;
        background: #ebe7df;
        border-radius: 8px;
        padding: 12px 14px;
    }

    .activity-dot {
        width: 9px;
        height: 9px;
        border-radius: 999px;
    }

    .activity-dot.success {
        background: #2ecc71;
    }

    .activity-dot.warning {
        background: #f59e0b;
    }

    .activity-dot.danger {
        background: #e5484d;
    }

    .activity-dot.neutral {
        background: #9ca3af;
    }

    .activity-text {
        color: #3a2d25;
        font-size: 15px;
    }

    .activity-text strong {
        color: #241b15;
        font-weight: 800;
    }

    .activity-row time {
        color: #8a6f5f;
        font-size: 12px;
        white-space: nowrap;
    }

    @media (max-width: 900px) {
        .dashboard-top-grid,
        .dashboard-middle-grid {
            grid-template-columns: 1fr;
        }

        .dashboard-page {
            padding: 18px;
        }
    }
</style>

    @php
        $chartData = $metrics['monthlyCustomersChart'] ?? [];
        $maxValue = collect($chartData)->max('total') ?: 1;

        $monthlyCustomers = $metrics['monthlyCustomers'] ?? 0;
        $growth = $metrics['customerGrowth'] ?? 0;
        $unansweredCustomers = $metrics['unansweredCustomers'] ?? 0;
        $totalConversations = $metrics['totalConversations'] ?? 0;
        $csEscalations = $metrics['csEscalations'] ?? 0;
        $averageResponseTime = $metrics['averageResponseTime']['formatted'] ?? '0s';
        $recentActivities = $metrics['recentActivities'] ?? [];
        $currentMonthLabel = $metrics['currentMonthLabel'] ?? now()->format('F Y');
    @endphp

    <div class="dashboard-page">

        {{-- TOP ROW --}}
        <div class="dashboard-top-grid">
            <section class="dashboard-card monthly-card">
                <h3 class="card-title">Monthly Customers</h3>

                <div class="monthly-chart">
                    @foreach ($chartData as $month)
                        @php
                            $height = max(30, ($month['total'] / $maxValue) * 95);
                        @endphp

                        <div class="chart-column">
                            <div
                                class="chart-bar {{ $month['is_current'] ? 'is-active' : '' }}"
                                style="height: {{ $height }}px;"
                            ></div>

                            <span>{{ $month['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="monthly-info">
                    <div class="monthly-label-row">
                        <strong>{{ $currentMonthLabel }}</strong>

                        <span class="growth-badge {{ $growth >= 0 ? 'positive' : 'negative' }}">
                            {{ $growth >= 0 ? '▲' : '▼' }}
                            {{ abs($growth) }}%
                        </span>
                    </div>

                    <div class="monthly-number">
                        {{ number_format($monthlyCustomers, 0, ',', '.') }}
                    </div>
                </div>
            </section>

            <section class="dashboard-card unanswered-card">
                <h3 class="card-title">Unanswered Customers</h3>

                <div class="unanswered-number">
                    {{ $unansweredCustomers }}
                </div>

                <p>Menunggu respons CS</p>
            </section>
        </div>

        {{-- MIDDLE ROW --}}
        <div class="dashboard-middle-grid">
            <section class="dashboard-card metric-card">
                <h3 class="card-title">Total Percakapan</h3>

                <div class="metric-number">
                    {{ number_format($totalConversations, 0, ',', '.') }}
                </div>

                <p>Bulan ini</p>
            </section>

            <section class="dashboard-card metric-card">
                <h3 class="card-title">Eskalasi ke CS</h3>

                <div class="metric-number danger">
                    {{ $csEscalations }}
                </div>

                <p>Perlu penanganan</p>
            </section>

            <section class="dashboard-card metric-card">
                <h3 class="card-title">Avg. Response Time</h3>

                <div class="metric-number response-time">
                    {{ $averageResponseTime }}
                </div>

                <p>Rata-rata bot / CS</p>
            </section>
        </div>

        {{-- RECENT ACTIVITY --}}
        <section class="dashboard-card activity-card">
            <h3 class="card-title">Aktivitas Terbaru</h3>

            <div class="activity-list">
                @forelse ($recentActivities as $activity)
                    <div class="activity-row">
                        <span class="activity-dot {{ $activity['color'] ?? 'neutral' }}"></span>

                        <div class="activity-text">
                            {{ $activity['message'] ?? '-' }}

                            @if (!empty($activity['actor_name']))
                                <strong>{{ $activity['actor_name'] }}</strong>
                            @endif
                        </div>

                        <time>{{ $activity['time'] ?? '-' }}</time>
                    </div>
                @empty
                    <div class="activity-row">
                        <span class="activity-dot neutral"></span>

                        <div class="activity-text">
                            Belum ada aktivitas terbaru.
                        </div>

                        <time>-</time>
                    </div>
                @endforelse
            </div>
        </section>

    </div>
@endsection