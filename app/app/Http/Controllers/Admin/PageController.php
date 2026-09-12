<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    // GET /admin/privacy-policy
    public function editPrivacy()
    {
        $page = Page::bySlug('privacy-policy', 'Privacy Policy');
        return view('admin.privacy.edit', compact('page'));
    }

    // PUT /admin/privacy-policy
    public function updatePrivacy(Request $request)
    {
        $data = $request->validate(['content' => 'required|string']);
        Page::bySlug('privacy-policy', 'Privacy Policy')->update($data);
        return back()->with('success', 'Privacy policy saved.');
    }

    // GET /admin/terms-of-uses
    public function editTerms()
    {
        $page = Page::bySlug('terms-of-uses', 'Terms of Uses');
        return view('admin.terms.edit', compact('page'));
    }

    // PUT /admin/terms-of-uses
    public function updateTerms(Request $request)
    {
        $data = $request->validate(['content' => 'required|string']);
        Page::bySlug('terms-of-uses', 'Terms of Uses')->update($data);
        return back()->with('success', 'Terms of uses saved.');
    }
}
