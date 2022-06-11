<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class MentorController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $mentors = DB::table('goal_mentors')
            ->join('users', 'goal_mentors.mentor_id', '=', 'users.id')
            ->select('goal_mentors.mentor_id', 'users.username')
            ->distinct()
            ->get();

        $goals = DB::table('goal_mentors')
            ->join('users', 'goal_mentors.mentor_id', '=', 'users.id')
            ->join('goals', 'goal_mentors.goal_id', '=', 'goals.id')
            ->select('goal_mentors.mentor_id', 'goal_mentors.goal_id', 'goals.id', 'goals.title', 'goals.description', 'goals.created_at')
            ->distinct()
            ->get();

        return view('my-mentors', ['user' => $user, 'mentors' => $mentors, 'goals' => $goals]);
    }
}
