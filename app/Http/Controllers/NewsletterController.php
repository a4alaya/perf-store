<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterRequest;
use App\Models\NewsletterSubscriber;
use App\Notifications\NewsletterConfirmationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;

class NewsletterController extends Controller
{
    public function store(NewsletterRequest $request): RedirectResponse
    {
        NewsletterSubscriber::updateOrCreate(
            ['email' => $request->string('email')->lower()->toString()],
            ['locale' => app()->getLocale(), 'subscribed_at' => now(), 'unsubscribed_at' => null],
        );

        Notification::route('mail', $request->email)->notify(new NewsletterConfirmationNotification());

        return back()->with('status', __('You are subscribed to Maison De Mystere updates.'));
    }
}
