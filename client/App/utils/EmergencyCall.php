<?php

namespace Socket\Client\utils;

use Socket\Client\traits\ENVConfig;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Pusher\Pusher;
use Pusher\PusherException;

class EmergencyCall
{
    use ENVConfig;

    /**
     * @param $request
     * @return array|true[]
     */
    public function call($request): array
    {
        try {
            $env = $this->env();
            $options = array(
                'cluster' => 'eu',
                'useTLS' => false
            );
            $pusher = new Pusher(
                $env["PUSHER_AUTH_KEY"],
                $env["PUSHER_SECRET"],
                $env["PUSHER_API_ID"],
                $options
            );

            if ($request->callBool) {
                $message="{$request->message}{$request->user_full_name} Tarafından";
            } else{
                $message="";
            }
            $data['callBool'] = $request->callBool;
            $data['howler'] = $request->howler;
            $data['message'] = $message;
            $pusher->trigger('call', "{$request->user_service}", $data);
            $result = ["operation" => true];

        } catch (PusherException|GuzzleException $e) {
            $result = ["operation" => false, "PusherException|GuzzleException" => $e];
        }
        return $result;


    }
}