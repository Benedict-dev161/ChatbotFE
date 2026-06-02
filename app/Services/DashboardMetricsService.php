<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardMetricsService
{
    /**
     * Mengambil seluruh data metrik untuk dashboard.
     */
    public function getMetrics(): array
    {
        $now = Carbon::now();

        return [
            'monthlyCustomersChart' => $this->getMonthlyCustomersChart($now),
            'monthlyCustomers' => $this->getCurrentMonthCustomers($now),
            'customerGrowth' => $this->getCustomerGrowthPercentage($now),
            'unansweredCustomers' => $this->getUnansweredCustomers(),
            'totalConversations' => $this->getTotalConversationsThisMonth($now),
            'conversationCards' => $this->getConversationCountsPerCustomer(),
            'csEscalations' => $this->getOpenEscalations(),
            'averageResponseTime' => $this->getAverageResponseTime(),
            'recentActivities' => $this->getRecentActivities(),
            'currentMonthLabel' => $now->translatedFormat('F Y'),
        ];
    }

    /**
     * Data chart customer 12 bulan terakhir.
     */
    private function getMonthlyCustomersChart(Carbon $now): array
    {
        $start = $now->copy()->subMonths(11)->startOfMonth();

        $rawData = DB::table('users')
            ->selectRaw('YEAR(created_at) AS year, MONTH(created_at) AS month, COUNT(*) AS total')
            ->where('role', 'customer')
            ->where('created_at', '>=', $start)
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->get();

        $chart = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $year = (int) $date->year;
            $month = (int) $date->month;

            $match = $rawData->first(function ($item) use ($year, $month) {
                return (int) $item->year === $year && (int) $item->month === $month;
            });

            $chart[] = [
                'label' => $date->format('M'),
                'month' => $month,
                'year' => $year,
                'total' => $match ? (int) $match->total : 0,
                'is_current' => $month === (int) $now->month && $year === (int) $now->year,
            ];
        }

        return $chart;
    }

    /**
     * Jumlah customer baru bulan ini.
     */
    private function getCurrentMonthCustomers(Carbon $now): int
    {
        return DB::table('users')
            ->where('role', 'customer')
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->count();
    }

    /**
     * Persentase pertumbuhan customer dibanding bulan sebelumnya.
     */
    private function getCustomerGrowthPercentage(Carbon $now): float
    {
        $current = $this->getCurrentMonthCustomers($now);

        $previousMonth = $now->copy()->subMonth();

        $previous = DB::table('users')
            ->where('role', 'customer')
            ->whereYear('created_at', $previousMonth->year)
            ->whereMonth('created_at', $previousMonth->month)
            ->count();

        if ($previous === 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Customer yang menunggu respons CS.
     */
    private function getUnansweredCustomers(): int
    {
        return DB::table('conversations')
            ->where('current_status', 'waiting_cs')
            ->count();
    }

    /**
     * Total percakapan bulan ini.
     */
    private function getTotalConversationsThisMonth(Carbon $now): int
    {
        return DB::table('conversations')
            ->whereYear('started_at', $now->year)
            ->whereMonth('started_at', $now->month)
            ->count();
    }

    /**
     * Jumlah percakapan per customer.
     * Ini berguna untuk card/list customer.
     */
    private function getConversationCountsPerCustomer(): array
    {
        return DB::table('users')
            ->leftJoin('conversations', 'users.user_id', '=', 'conversations.customer_id')
            ->select(
                'users.user_id',
                'users.full_name',
                'users.phone',
                DB::raw('COUNT(conversations.conversation_id) AS total_conversations'),
                DB::raw("SUM(CASE WHEN conversations.current_status = 'active' THEN 1 ELSE 0 END) 
                    AS active_conversations"),
                DB::raw("SUM(CASE WHEN conversations.current_status = 'waiting_cs' THEN 1 ELSE 0 END) 
                    AS waiting_conversations")
            )
            ->where('users.role', 'customer')
            ->groupBy('users.user_id', 'users.full_name', 'users.phone')
            ->orderByDesc('total_conversations')
            ->limit(10)
            ->get()
            ->map(fn ($item) => [
                'user_id' => $item->user_id,
                'full_name' => $item->full_name,
                'phone' => $item->phone,
                'total_conversations' => (int) $item->total_conversations,
                'active_conversations' => (int) $item->active_conversations,
                'waiting_conversations' => (int) $item->waiting_conversations,
            ])
            ->toArray();
    }

    /**
     * Eskalasi ke CS yang masih perlu penanganan.
     */
    private function getOpenEscalations(): int
    {
        return DB::table('escalations')
            ->whereIn('status', ['open', 'handled'])
            ->count();
    }

    /**
     * Menghitung rata-rata response time.
     *
     * Logic:
     * - Ambil pesan dari customer.
     * - Cari pesan berikutnya dalam conversation yang dikirim oleh AI/CS/admin.
     * - Hitung selisih detik.
     * - Ambil rata-rata.
     */
    private function getAverageResponseTime(): array
    {
        $customerMessages = DB::table('messages')
            ->join('users AS sender', 'sender.user_id', '=', 'messages.sender_id')
            ->select(
                'messages.message_id',
                'messages.conversation_id',
                'messages.created_at'
            )
            ->where('sender.role', 'customer')
            ->where(function ($query) {
                $query->whereNull('messages.deleted_at')
                    ->orWhere('messages.is_deleted', 0);
            })
            ->orderBy('messages.created_at')
            ->limit(500)
            ->get();

        $responseTimes = [];

        foreach ($customerMessages as $customerMessage) {
            $response = DB::table('messages')
                ->join('users AS responder', 'responder.user_id', '=', 'messages.sender_id')
                ->where('messages.conversation_id', $customerMessage->conversation_id)
                ->where('messages.created_at', '>', $customerMessage->created_at)
                ->whereIn('responder.role', ['ai', 'cs', 'admin'])
                ->where(function ($query) {
                    $query->whereNull('messages.deleted_at')
                        ->orWhere('messages.is_deleted', 0);
                })
                ->orderBy('messages.created_at')
                ->first();

            if ($response) {
                $start = Carbon::parse($customerMessage->created_at);
                $end = Carbon::parse($response->created_at);

                $responseTimes[] = $start->diffInSeconds($end);
            }
        }

        if (count($responseTimes) === 0) {
            return [
                'seconds' => 0,
                'formatted' => '0s',
            ];
        }

        $averageSeconds = (int) round(array_sum($responseTimes) / count($responseTimes));

        return [
            'seconds' => $averageSeconds,
            'formatted' => $this->formatSeconds($averageSeconds),
        ];
    }

    /**
     * Ambil aktivitas terbaru dari system_logs.
     */
    private function getRecentActivities(): array
    {
        return DB::table('system_logs')
            ->leftJoin('users', 'users.user_id', '=', 'system_logs.user_id')
            ->leftJoin('conversations', 'conversations.conversation_id', '=', 'system_logs.conversation_id')
            ->select(
                'system_logs.log_id',
                'system_logs.event_type',
                'system_logs.message',
                'system_logs.created_at',
                'users.full_name AS actor_name',
                'conversations.current_status'
            )
            ->orderByDesc('system_logs.created_at')
            ->limit(6)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->log_id,
                    'type' => $log->event_type,
                    'message' => $log->message,
                    'actor_name' => $log->actor_name,
                    'status' => $log->current_status,
                    'time' => Carbon::parse($log->created_at)->diffForHumans(),
                    'color' => $this->activityColor($log->event_type),
                ];
            })
            ->toArray();
    }

    /**
     * Format detik menjadi tampilan seperti 1m 24s.
     */
    private function formatSeconds(int $seconds): string
    {
        if ($seconds < 60) {
            return "{$seconds}s";
        }

        $minutes = intdiv($seconds, 60);
        $remainingSeconds = $seconds % 60;

        return "{$minutes}m {$remainingSeconds}s";
    }

    /**
     * Warna status untuk activity dot.
     */
    private function activityColor(string $eventType): string
    {
        return match ($eventType) {
            'conversation_closed',
            'service_request_approved',
            'cs_reply' => 'success',

            'escalation_created',
            'cs_assigned' => 'warning',

            'customer_waiting',
            'error',
            'failed' => 'danger',

            default => 'neutral',
        };
    }
}
