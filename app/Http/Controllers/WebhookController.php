<?php

namespace App\Http\Controllers;

use App\Twilio;
use App\Webhook;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, $this->rules());

        $webhook = Webhook::create($request->only(['title', 'notification_message']));

        $url = config('app.url') . "/webhook/{$webhook->identifier}";

        $result = [
            'message' => "Webhook has been created successfully",
            'data' => "Webhook URL is {$url}"
        ];

        return response()->json($result);
    }

    public function dispatchNotification($webhook, Twilio $twilio)
    {
        $webhook = Webhook::whereIdentifier($webhook)->firstOrFail();

        $twilio->notify(env('TWILIO_SMS_TO'), $webhook->notification_message);

        $result = [
            'message' => 'Message has been delivered'
        ];

        return response()->json($result);
    }

    protected function rules()
    {
        return [
            'title' => 'required',
            'notification_message' => 'required'
        ];
    }
}
