<?php

namespace App\Http\Controllers;

use App\Models\ChatUser;

class StaffController extends Controller
{
    public function index()
    {
        $staff = ChatUser::query()
            ->whereIn('role', ['cs', 'admin'])
            ->orderBy('full_name')
            ->get();

        return view('staff.index', compact('staff'));
    }
}
