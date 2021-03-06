<?php

namespace App\Services;

use App\Contracts\GoalServiceInterface;
use App\Models\Goal;
use App\Models\ActionPlan;
use App\Models\Activity;
use App\Models\GoalComment;
use App\Models\User;
use App\Models\CommentVote;
use Illuminate\Http\Request;

class GoalService implements GoalServiceInterface
{
    private $user;
    private $actionPlans;

    public function forUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get all the goals for the current user
     */
    public function getGoals()
    {
        $goals = Goal::select(
            'id',
            'user_id',
            'title',
            'description',
            'smart_goal',
            'due_at',
            'created_at',
            'updated_at'
        )->where('user_id', $this->user->id)->get();

        return $goals;
    }

    /**
     * Get goals by the goal ID
     *
     * @param $goalid
     * @return App\Models\Goal
     */
    public function getGoalByID($goalid)
    {
        return Goal::find($goalid);
    }

    /**
     * Get all action plans for a specific goal
     *
     * @param $goalid
     * @return Collection
     */
    public function getActionPlans($goalid)
    {
        $actionPlans = ActionPlan::select('id', 'goal_id', 'title', 'ap_status', 'start_at', 'end_at')
            ->where('goal_id', $goalid)
            ->get();

        $this->actionPlans = $actionPlans;

        return $actionPlans;
    }

    /**
     * Get all activities for all action plans of the current goal
     *
     * @return Collection
     */
    public function getActivities()
    {
        // Initializes an array that holds all action_plan_ID for this goal
        $actionPlansIdArray = array();
        foreach ($this->actionPlans as $actionPlan) {
            array_push($actionPlansIdArray, $actionPlan['id']);
        }

        // Retrieve all activities within the goal
        $activities = Activity::select('activities.id', 'action_plan_id', 'activities.title', 'a_status', 
        'activities.created_at', 'activities.updated_at', 'goals.id as goal_id')
            ->join('action_plans', 'action_plan_id', '=', 'action_plans.id')
            ->join('goals', 'action_plans.goal_id', '=', 'goals.id')
            ->whereIn('action_plan_id', $actionPlansIdArray)
            ->get();

        return $activities;
    }

    /**
     * Get all comments for the specified goal id
     *
     * @param $goalid
     * @return Collection
     */
    public function getComments($goalid)
    {
        $comments = GoalComment::select('id', 'goal_id', 'user_id', 'body', 'created_at', 'updated_at')
            ->where('goal_id', $goalid)
            ->latest()
            ->get();

        //Append name to each comment
        $users = User::all();
        foreach ($comments as $comment) {
            foreach ($users as $user) {
                if ($comment['user_id'] == $user['id']) {
                    $comment['username'] = $user['name'];
                    $comment['timeinterval'] = $this->getInterval($comment['created_at']);

                    $comment['timeintervalDay'] = false;
                    if($comment['timeinterval'] >= 24) {
                        $comment['timeintervalDay'] = ceil($comment['timeinterval']/24);
                    }

                    $comment['timeintervalMinutes'] = false;
                    if($comment['timeinterval'] <= 0) {
                        $comment['timeintervalMinutes'] = $this->getIntervalMinutes($comment['created_at']);
                    }
                    break;
                }
            }

            //Append votetype to the each comment
            $vote = CommentVote::find($comment['id']);
            if ($vote != null) {
                $comment['commentvote'] = $vote['vote_type'];
                $comment['commentvoteid'] = $vote['id'];
            } else {
                $comment['commentvote'] = 0;
                $comment['commentvoteid'] = null;
            }
        }

        return $comments;
    }

    public function getInterval($from_date)
    {
        $timeformat = 'Y-m-d H:i:s';
        //Date in Date object
        $duedate = date($timeformat, strtotime($from_date));
        $currdate = date($timeformat);

        //Calculate difference in hours
        $hours = (strtotime($duedate) - strtotime($currdate)) / 3600;
        //$hours -= 8; //Correct the timezone
        $hours = (int)$hours; //Convert to integer

        return abs($hours);
    }

    public function getIntervalMinutes($from_date)
    {
        $timeformat = 'Y-m-d H:i:s';
        //Date in Date object
        $duedate = date($timeformat, strtotime($from_date));
        $currdate = date($timeformat);

        //Calculate difference in hours
        $minutes = (strtotime($duedate) - strtotime($currdate)) / 60;
        //$hours -= 8; //Correct the timezone
        $minutes = (int)$minutes; //Convert to integer

        return abs($minutes);
    }

    // Returns -> A goal's percentage of completion
    // Param -> $goalId
    public static function getPercentageCompleted($goalId)
    {
        // Get activitiesCount
        $activitiesCount = Activity::select('id', 'action_plan_id')
            ->join('action_plans', 'action_plans.id', '=', 'activities.action_plan_id')
            ->join('goals', 'goals.id', '=', 'action_plans.goal_id',)
            ->where('goals.id', '=', $goalId)
            ->count();

        if ($activitiesCount == 0) {
            return 0;
        }

        // Get completedActivitiesCount
        $completedActivitiesCount = Activity::select('id')
            ->join('action_plans', 'action_plans.id', '=', 'activities.action_plan_id')
            ->join('goals', 'goals.id', '=', 'action_plans.goal_id',)
            ->where('goals.id', '=', $goalId)
            ->where('a_status', '=', Activity::ACTIVITY_COMPLETED)
            ->count();

        // Compute and return percentage of completion
        return number_format($completedActivitiesCount / $activitiesCount, 2) * 100;
    }

    // To update last_viewed_at field in table GOALS whenever user enters a goal page
    public function queryUpdateLastViewedAt($goalId) {
        Goal::select('id', 'last_viewed_at')
            ->where('id', '=', $goalId)
            ->update(['last_viewed_at' => now()]);
    }
}
