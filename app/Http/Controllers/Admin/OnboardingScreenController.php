<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingScreen;
use Illuminate\Http\Request;

class OnboardingScreenController extends Controller
{
    // GET /admin/onboarding-screens
    public function index()
    {
        $screens = OnboardingScreen::orderBy('position')->get();
        return view('admin.onboarding.index', compact('screens'));
    }

    // POST /admin/onboarding-screens  (Add Onboarding Screen -> Save)
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $nextPosition = (OnboardingScreen::max('position') ?? 0) + 1;

        OnboardingScreen::create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'image_path'  => $request->file('image')->store('onboarding', 'public'),
            'position'    => $nextPosition,
        ]);

        return back()->with('success', 'Onboarding screen added.');
    }

    // PUT /admin/onboarding-screens/{screen}
    public function update(Request $request, OnboardingScreen $screen)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('onboarding', 'public');
        }

        $screen->update($data);

        return back()->with('success', 'Onboarding screen updated.');
    }

    // PATCH /admin/onboarding-screens/reorder  (drag handle re-order)
    public function reorder(Request $request)
    {
        $data = $request->validate(['order' => 'required|array', 'order.*' => 'exists:onboarding_screens,id']);

        foreach ($data['order'] as $index => $id) {
            OnboardingScreen::where('id', $id)->update(['position' => $index + 1]);
        }

        return response()->json(['ok' => true]);
    }

    // DELETE /admin/onboarding-screens/{screen}
    public function destroy(OnboardingScreen $screen)
    {
        $screen->delete();
        return back()->with('success', 'Onboarding screen deleted.');
    }
}
