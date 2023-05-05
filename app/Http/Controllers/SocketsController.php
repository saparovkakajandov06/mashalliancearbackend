<?php


namespace App\Http\Controllers;

use Illuminate\Broadcasting\Broadcasters\PusherBroadcaster;
use Illuminate\Http\Request;
use Pusher\Pusher;
use Pusher\PusherException;

class SocketsController extends Controller
{
    public function connect(Request $request)
    {
        try {
            $broadcaster = new PusherBroadcaster(
                new Pusher(
                    env("PUSHER_APP_KEY"),
                    env("PUSHER_APP_SECRET"),
                    env("PUSHER_APP_ID"),
                    []
                )
            );
        }
        catch (PusherException $e) {

        }

        return $broadcaster->validAuthenticationResponse($request, []);
    }

}
