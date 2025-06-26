<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Inertia\Inertia;

class CronjobController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:cron-job');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $jobs = [
            [
                'title' => __('Execute Schedule Messages'),
                'url' => 'curl -s ' . url('/cron/execute-schedule'),
                'time' => __('Everyday')
            ],
            [
                'title' => __('Notify to customer before expire the subscription'),
                'url' => 'curl -s ' . url('/cron/notify-to-user'),
                'time' => __('Everyday')
            ]
        ];

        return Inertia::render('Admin/Cron/Index', compact('jobs'));
    }
}
