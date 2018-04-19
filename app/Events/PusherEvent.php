<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PusherEvent extends Event implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $info;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($info)
    {
        //
        $this->info = $info;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // 公共频道
        return ['my-channel'];

        // 私有频道
        // return new PrivateChannel('my-channel');
    }

    /**
     * 指定广播事件(对应前端的事件)
     * @return string
     */
    public function broadcastAs()
    {
        return 'my-event';
    }
 
    /**
     * 获取广播数据,默认是广播的public属性的数据
     */
    public function broadcastWith()
    {
        return $this->info;
    }
}
