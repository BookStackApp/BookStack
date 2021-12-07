<?php

namespace BookStack\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * Show all webhooks configured in the system.
     */
    public function index()
    {
        return view('settings.webhooks.index');
    }
}
