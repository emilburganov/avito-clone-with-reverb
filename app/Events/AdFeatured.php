<?php

namespace App\Events;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdFeatured implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Ad $ad;
    private Authenticatable|User $user;

    /**
     * Create a new event instance.
     */
    public function __construct(Ad $ad, Authenticatable|User $user)
    {
        $this->ad = $ad;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ad-featured.' . $this->ad->user_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'message' => "Ваше объявление \"{$this->ad->title}\" добавлено в избранные другим пользователем!\nЭлектронная почта этого пользователя - {$this->user->email}",
        ];
    }
}
