<?php

namespace App\Listeners;

use App\Events\AdminAddedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyAddedAdminListener
{
    /**
     * Handle the event.
     *
     * @param  App\Events\AdminAddedEvent  $event
     * @return void
     */
    public function handle(AdminAddedEvent $event)
    {
        $user = $event->user;

        Mail::send('admin.adminAdded', [], function (Message $message) use ($user) {
            $message->to($user->email);
            $message->subject('You have been added to Admin!');
        });
    }
}
