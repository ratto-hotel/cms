<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserCurrency;
use App\Models\UserSetting;
use Carbon\Carbon;

class LeaderboardController extends Controller
{
    public function __invoke()
    {
        $staffUsers = User::select('id')->where('rank', '>=', setting('min_staff_rank'))->get()->pluck('id');

        $date = Carbon::now();
        $date->subtract('month', 1);
        $timeInMilliseconds = $date->valueOf();

        $duckets = UserCurrency::whereNotIn('user_id', $staffUsers)
            ->where('type', 0)
            ->leftJoin('users', 'users.last_login', '>=', $timeInMilliseconds)
            ->orderByDesc('amount')
            ->take(9)
            ->with(['user:id,username,look'])
            ->get();

        $diamonds = UserCurrency::whereNotIn('user_id', $staffUsers)
            ->where('type', 5)
            ->orderByDesc('amount')
            ->take(9)
            ->with(['user:id,username,look'])
            ->get();


        $mostOnline = UserSetting::whereNotIn('user_id', $staffUsers)
            ->select('user_id', 'online_time')
            ->orderByDesc('online_time')
            ->take(9)
            ->with('user:id,username,look')
            ->get();

        $respectsReceived = UserSetting::whereNotIn('user_id', $staffUsers)
            ->select('user_id', 'respects_received')
            ->orderByDesc('respects_received')
            ->take(9)
            ->with('user:id,username,look')
            ->get();

        $achievementScores = UserSetting::whereNotIn('user_id', $staffUsers)
            ->select('user_id', 'achievement_score')
            ->orderByDesc('achievement_score')
            ->take(9)
            ->with('user:id,username,look')
            ->get();

        $credits = User::whereNotIn('id', $staffUsers)->orderByDesc('credits')->take(9)->get();

        return view('leaderboard', [
            'credits' => $credits,
            'duckets' => $duckets,
            'diamonds' => $diamonds,
            'mostOnline' => $mostOnline,
            'respectsReceived' => $respectsReceived,
            'achievementScores' => $achievementScores,
        ]);
    }
}
