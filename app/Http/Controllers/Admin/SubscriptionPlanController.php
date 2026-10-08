<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    // GET /admin/subscription-plans
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('position')->get();
        return view('admin.subscriptionplans.index', compact('plans'));
    }

    // POST /admin/subscription-plans  (Add Plan modal -> Save)
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'price'          => 'required|numeric|min:0',
            'currency'       => 'required|string|max:3',
            'duration_days'  => 'required|integer|min:1',
            'description'    => 'nullable|string|max:255',
            'is_best_value'  => 'nullable|boolean',
        ]);

        $data['position'] = (SubscriptionPlan::max('position') ?? 0) + 1;
        SubscriptionPlan::create($data);

        return back()->with('success', 'Plan created.');
    }

    // PUT /admin/subscription-plans/{plan}
    public function update(Request $request, SubscriptionPlan $plan)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'price'          => 'required|numeric|min:0',
            'currency'       => 'required|string|max:3',
            'duration_days'  => 'required|integer|min:1',
            'description'    => 'nullable|string|max:255',
            'is_best_value'  => 'nullable|boolean',
        ]);

        $plan->update($data);

        return back()->with('success', 'Plan updated.');
    }

    // PATCH /admin/subscription-plans/{plan}/toggle  (active/inactive switch)
    public function toggle(Request $request, SubscriptionPlan $plan)
    {
        $plan->update(['is_active' => $request->boolean('value')]);
        return response()->json(['ok' => true]);
    }

    // DELETE /admin/subscription-plans/{plan}
    public function destroy(SubscriptionPlan $plan)
    {
        $plan->delete();
        return back()->with('success', 'Plan deleted.');
    }
}
