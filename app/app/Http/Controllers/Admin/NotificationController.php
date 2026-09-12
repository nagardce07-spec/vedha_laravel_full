<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Customer;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // GET /admin/notifications
    public function index()
    {
        $notifications = AppNotification::latest()->paginate(10);
        return view('admin.notifications.index', compact('notifications'));
    }

    // POST /admin/notifications  (Add Notification modal -> Save)
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'send_now'    => 'nullable|boolean',
        ]);

        $notification = AppNotification::create([
            'title'       => $data['title'],
            'description' => $data['description'],
        ]);

        if ($request->boolean('send_now')) {
            $this->dispatchPush($notification);
        }

        return back()->with('success', 'Notification saved.');
    }

    // PUT /admin/notifications/{notification}
    public function update(Request $request, AppNotification $notification)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        $notification->update($data);
        return back()->with('success', 'Notification updated.');
    }

    // POST /admin/notifications/{notification}/send  (paper-plane icon -> resend)
    public function send(AppNotification $notification)
    {
        $this->dispatchPush($notification);
        return back()->with('success', 'Push notification sent.');
    }

    // DELETE /admin/notifications/{notification}
    public function destroy(AppNotification $notification)
    {
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }

    // Sends the push via FCM to every customer's stored device token.
    // Swap in your real FCM/OneSignal call here.
    private function dispatchPush(AppNotification $notification): void
    {
        $tokens = Customer::whereNotNull('fcm_token')->pluck('fcm_token');

        // Example (Firebase Cloud Messaging HTTP v1) — implement with your credentials:
        // foreach ($tokens->chunk(500) as $chunk) { FcmService::sendMulticast($chunk, $notification->title, $notification->description); }

        $notification->update(['sent' => true, 'sent_at' => now()]);
    }
}
