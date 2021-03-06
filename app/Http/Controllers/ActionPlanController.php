<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActionPlanRequest;
use App\Http\Requests\UpdateActionPlanRequest;
use App\Models\ActionPlan;

class ActionPlanController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreActionPlanRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreActionPlanRequest $request)
    {   
        $goalActionPlan = new ActionPlan;
        $goalActionPlan->goal_id = $request->goal_id;
        $goalActionPlan->title = $request->title;
        $goalActionPlan->ap_status = ActionPlan::ACTIONPLAN_NOT_STARTED;
        $goalActionPlan->start_at = $request->start_at;
        $goalActionPlan->end_at = $request->end_at;
        $goalActionPlan->created_at = now();
        $goalActionPlan->updated_at = now();
        $goalActionPlan->save();

        return redirect()->route('goal.show', $request->goal_id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateActionPlanRequest  $request
     * @param  \App\Models\ActionPlan  $actionPlan
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateActionPlanRequest $request, $actionPlanID)
    {   
        $actionPlan = ActionPlan::find($actionPlanID);

        $actionPlan->title = $request->title;
        $actionPlan->start_at = $request->start_at;
        $actionPlan->end_at = $request->end_at;
        $actionPlan->updated_at = now();
        $actionPlan->save();

        return redirect()->route('goal.show', $request->goal_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ActionPlan  $actionPlan
     * @return \Illuminate\Http\Response
     */
    public function destroy(ActionPlan $actionPlan)
    {
        $actionPlan->delete();

        return redirect()->route('goal.show', $actionPlan['goal_id']);
    }
}
