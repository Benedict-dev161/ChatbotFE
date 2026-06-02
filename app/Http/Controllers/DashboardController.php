<?php

namespace App\Http\Controllers;

use App\Models\ChatUser;
use App\Models\Conversation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        Carbon::setLocale('id');

        $now = Carbon::now();

        /*
        |--------------------------------------------------------------------------
        | 1. Monthly Customers
        |--------------------------------------------------------------------------
        | Menghitung jumlah customer baru bulan ini.
        */
        $monthlyCustomers = ChatUser::query()
            ->where('role', 'customer')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 2. Previous Month Customers
        |--------------------------------------------------------------------------
        | Digunakan untuk menghitung growth dibanding bulan sebelumnya.
        */
        $previousMonth = $now->copy()->subMonth();

        $previousMonthCustomers = ChatUser::query()
            ->where('role', 'customer')
            ->whereMonth('created_at', $previousMonth->month)
            ->whereYear('created_at', $previousMonth->year)
            ->count();

        $growth = $previousMonthCustomers > 0
            ? round((($monthlyCustomers - $previousMonthCustomers) / $previousMonthCustomers) * 100, 1)
            : ($monthlyCustomers > 0 ? 100 : 0);

        /*
        |--------------------------------------------------------------------------
        | 3. Chart Monthly Customers 12 Bulan Terakhir
        |--------------------------------------------------------------------------
        | Data untuk bar chart pada dashboard.
        */
        $monthlyCustomersChart = $this->getMonthlyCustomersChart($now);

        /*
        |--------------------------------------------------------------------------
        | 4. Unanswered Customers
        |--------------------------------------------------------------------------
        | Menghitung customer yang masih menunggu respons CS.
        */
        $unansweredCustomers = Conversation::query()
            ->where('current_status', 'waiting_cs')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 5. Total Conversations Bulan Ini
        |--------------------------------------------------------------------------
        */
        $totalConversations = Conversation::query()
            ->whereMonth('started_at', $now->month)
            ->whereYear('started_at', $now->year)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 6. Total Conversation per Customer Card
        |--------------------------------------------------------------------------
        | Berguna jika nanti kamu ingin menampilkan daftar customer + jumlah percakapannya.
        */
        $conversationCards = $this->getConversationCountsPerCustomer();

        /*
        |--------------------------------------------------------------------------
        | 7. Eskalasi ke CS
        |--------------------------------------------------------------------------
        | Menghitung escalation yang masih perlu ditangani.
        */
        $csEscalations = DB::table('escalations')
            ->whereIn('status', ['open', 'handled'])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 8. Average Response Time
        |--------------------------------------------------------------------------
        | Menghitung rata-rata waktu balasan dari AI/CS/admin setelah customer mengirim pesan.
        */
        $averageResponseTime = $this->getAverageResponseTime();

        /*
        |--------------------------------------------------------------------------
        | 9. Recent Activities
        |--------------------------------------------------------------------------
        | Mengambil aktivitas terbaru dari system_logs.
        */
        $recentActivities = $this->getRecentActivities();

        /*
        |--------------------------------------------------------------------------
        | 10. Metrics Array
        |--------------------------------------------------------------------------
        | Supaya Blade bisa memakai format rapi: $metrics['monthlyCustomers']
        */
        $metrics = [
            'monthlyCustomersChart' => $monthlyCustomersChart,
            'monthlyCustomers' => $monthlyCustomers,
            'previousMonthCustomers' => $previousMonthCustomers,
            'customerGrowth' => $growth,
            'unansweredCustomers' => $unansweredCustomers,
            'totalConversations' => $totalConversations,
            'conversationCards' => $conversationCards,
            'csEscalations' => $csEscalations,
            'averageResponseTime' => $averageResponseTime,
            'recentActivities' => $recentActivities,
            'currentMonthLabel' => $now->translatedFormat('F Y'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        | Saya return dua format:
        | 1. Variabel lama: $monthlyCustomers, $growth, dst.
        | 2. Variabel baru: $metrics
        |
        | Jadi Blade lama kamu tidak langsung rusak.
        */
        return view('dashboard.index', compact(
            'metrics',
            'monthlyCustomers',
            'previousMonthCustomers',
            'growth',
            'monthlyCustomersChart',
            'unansweredCustomers',
            'totalConversations',
            'conversationCards',
            'csEscalations',
            'averageResponseTime',
            'recentActivities',
            'now'
        ));
    }

    /**
     * Mengambil data customer baru selama 12 bulan terakhir.
     */
    private function getMonthlyCustomersChart(Carbon $now): array
    {
        $startDate = $now->copy()->subMonths(11)->startOfMonth();

        $rawData = ChatUser::query()
            ->selectRaw('YEAR(created_at) AS year, MONTH(created_at) AS month, COUNT(*) AS total')
            ->where('role', 'customer')
            ->where('created_at', '>=', $startDate)
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->get();

        $chart = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $year = (int) $date->year;
            $month = (int) $date->month;

            $matchedData = $rawData->first(function ($item) use ($year, $month) {
                return (int) $item->year === $year && (int) $item->month === $month;
            });

            $chart[] = [
                'label' => $date->format('M'),
                'month' => $month,
                'year' => $year,
                'total' => $matchedData ? (int) $matchedData->total : 0,
                'is_current' => $year === (int) $now->year && $month === (int) $now->month,
            ];
        }

        return $chart;
    }

    /**
     * Menghitung jumlah conversation per customer.
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
                    AS waiting_conversations"),
                DB::raw("SUM(CASE WHEN conversations.current_status = 'closed' THEN 1 ELSE 0 END) 
                    AS closed_conversations")
            )
            ->where('users.role', 'customer')
            ->groupBy('users.user_id', 'users.full_name', 'users.phone')
            ->orderByDesc('total_conversations')
            ->limit(10)
            ->get()
            ->map(function ($customer) {
                return [
                    'user_id' => $customer->user_id,
                    'full_name' => $customer->full_name,
                    'phone' => $customer->phone,
                    'total_conversations' => (int) $customer->total_conversations,
                    'active_conversations' => (int) $customer->active_conversations,
                    'waiting_conversations' => (int) $customer->waiting_conversations,
                    'closed_conversations' => (int) $customer->closed_conversations,
                ];
            })
            ->toArray();
    }

    /**
     * Menghitung rata-rata response time.
     *
     * Logic:
     * - Ambil pesan dari customer.
     * - Cari balasan pertama setelahnya dari AI/CS/admin.
     * - Hitung selisih waktu.
     * - Ambil rata-ratanya.
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
            ->where('messages.is_deleted', 0)
            ->whereNull('messages.deleted_at')
            ->whereNotNull('messages.created_at')
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
                ->where('messages.is_deleted', 0)
                ->whereNull('messages.deleted_at')
                ->whereNotNull('messages.created_at')
                ->orderBy('messages.created_at')
                ->first();

            if ($response) {
                $start = Carbon::parse($customerMessage->created_at);
                $end = Carbon::parse($response->created_at);

                $responseTimes[] = abs($start->diffInSeconds($end));
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
     * Mengambil aktivitas terbaru dari system_logs.
     */
    private function getRecentActivities(): array
    {
        return DB::table('system_logs')
            ->leftJoin('users', 'users.user_id', '=', 'system_logs.user_id')
            ->leftJoin('conversations', 'conversations.conversation_id', '=', 'system_logs.conversation_id')
            ->select(
                'system_logs.log_id',
                'system_logs.user_id',
                'system_logs.conversation_id',
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
                    'user_id' => $log->user_id,
                    'conversation_id' => $log->conversation_id,
                    'type' => $log->event_type,
                    'message' => $log->message,
                    'actor_name' => $log->actor_name,
                    'conversation_status' => $log->current_status,
                    'time' => Carbon::parse($log->created_at)->diffForHumans(),
                    'color' => $this->activityColor($log->event_type),
                ];
            })
            ->toArray();
    }

    /**
     * Format detik menjadi bentuk singkat.
     * Contoh:
     * 24 detik => 24s
     * 84 detik => 1m 24s
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
     * Menentukan warna dot aktivitas berdasarkan event_type.
     */
    private function activityColor(?string $eventType): string
    {
        return match ($eventType) {
            'conversation_closed',
            'service_request_approved',
            'cs_reply',
            'bot_reply' => 'success',

            'escalation_created',
            'cs_assigned',
            'form_requested' => 'warning',

            'customer_waiting',
            'error',
            'failed' => 'danger',

            default => 'neutral',
        };
    }
}
