<?php

namespace App\Events;

use App\Listeners\SendNotification;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Event;
use function Illuminate\Events\queueable;

class UserRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User $user
    )
    {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }

//    public function boot(): void
//    {
//        Event::Listen(function (SendNotification $event) {
//            //
//        });
//
//        Event::Listen(
//            SendNotification::class
//        );
//
//        Event::Listen(queueable(function (SendNotification $event) {
//            //
//        })->onConnection('redis')->onQueue('user')->delay(now()->addSeconds(5)));
//
//        Event::Listen(queueable(function (SendNotification $event) {
//            //
//        })->catch(function (SendNotification $event, Throwable $e) {
//            //
//        }));
//
//        Event::Listen('event.*', function (string $sendNotification, array $data) {
//            //
//        });
//    }
}
