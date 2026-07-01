<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students'      => DB::selectOne("SELECT COUNT(*) as count FROM STUDENT")->count,
            'total_scholarships'  => DB::selectOne("SELECT COUNT(*) as count FROM SCHOLARSHIP_PROGRAM")->count,
            'total_applications'  => DB::selectOne("SELECT COUNT(*) as count FROM APPLICATION")->count,
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
