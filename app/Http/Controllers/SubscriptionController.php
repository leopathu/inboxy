<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\SuppressionList;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    /**
     * Show confirmation page and confirm subscription.
     */
    public function confirm(string $token): Response
    {
        $subscriber = Subscriber::findByConfirmationToken($token);

        if (!$subscriber) {
            return Inertia::render('Subscription/Invalid', [
                'message' => 'Invalid or expired confirmation link.',
            ]);
        }

        // Already confirmed
        if ($subscriber->status === Subscriber::STATUS_SUBSCRIBED) {
            return Inertia::render('Subscription/AlreadyConfirmed', [
                'message' => 'Your subscription has already been confirmed.',
                'list' => $subscriber->list->only(['id', 'name']),
            ]);
        }

        // Confirm the subscription
        $subscriber->confirm();
        $subscriber->update(['confirmation_token' => null]); // Clear token after use

        return Inertia::render('Subscription/Confirmed', [
            'message' => $subscriber->list->subscribe_success_message ?? 'Thank you for confirming your subscription!',
            'list' => $subscriber->list->only(['id', 'name']),
        ]);
    }

    /**
     * Show unsubscribe page.
     */
    public function showUnsubscribe(string $token): Response
    {
        $subscriber = Subscriber::findByUnsubscribeToken($token);

        if (!$subscriber) {
            return Inertia::render('Subscription/Invalid', [
                'message' => 'Invalid unsubscribe link.',
            ]);
        }

        return Inertia::render('Subscription/Unsubscribe', [
            'token' => $token,
            'subscriber' => $subscriber->only(['id', 'email', 'first_name', 'last_name']),
            'list' => $subscriber->list->only(['id', 'name', 'from_name']),
        ]);
    }

    /**
     * Process unsubscribe request.
     */
    public function unsubscribe(Request $request, string $token)
    {
        $subscriber = Subscriber::findByUnsubscribeToken($token);

        if (!$subscriber) {
            return Inertia::render('Subscription/Invalid', [
                'message' => 'Invalid unsubscribe link.',
            ]);
        }

        // Already unsubscribed
        if ($subscriber->status === Subscriber::STATUS_UNSUBSCRIBED) {
            return Inertia::render('Subscription/AlreadyUnsubscribed', [
                'message' => 'You have already unsubscribed from this list.',
                'list' => $subscriber->list->only(['id', 'name']),
            ]);
        }

        // Unsubscribe
        $subscriber->unsubscribe();

        // Optionally add to suppression list
        if ($request->boolean('suppress', false)) {
            SuppressionList::suppress(
                $subscriber->list->brand_id,
                $subscriber->email,
                'unsubscribed',
                'Unsubscribed via unsubscribe link'
            );
        }

        return Inertia::render('Subscription/Unsubscribed', [
            'message' => $subscriber->list->unsubscribe_success_message ?? 'You have been unsubscribed successfully.',
            'list' => $subscriber->list->only(['id', 'name']),
        ]);
    }
}
