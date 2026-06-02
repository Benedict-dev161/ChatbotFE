<?php

namespace App\Http\Controllers;

use App\Models\ChatUser;

class StaffController extends Controller
{
    public function index()
    {
        $staffStats = ChatUser::query()
            ->from('users as staff')
            ->leftJoin(
                'conversations as conversations',
                'conversations.assigned_cs_id',
                '=',
                'staff.user_id'
            )
            ->select(
                'staff.user_id',
                'staff.full_name',
                'staff.email',
                'staff.phone',
                'staff.status'
            )
            ->selectRaw(
                'COUNT(DISTINCT conversations.customer_id) AS total_cust'
            )
            ->selectRaw(
                "COUNT(DISTINCT CASE 
                    WHEN conversations.current_status = 'closed' 
                    THEN conversations.customer_id 
                END) AS finished_cust"
            )
            ->selectRaw(
                "COUNT(DISTINCT CASE 
                    WHEN conversations.current_status IN ('active', 'waiting_cs') 
                    THEN conversations.customer_id 
                END) AS ongoing_cust"
            )
            ->where('staff.role', 'cs')
            ->groupBy(
                'staff.user_id',
                'staff.full_name',
                'staff.email',
                'staff.phone',
                'staff.status'
            )
            ->orderBy('staff.full_name')
            ->get();

        return view('staff.index', compact('staffStats'));
    }
}
