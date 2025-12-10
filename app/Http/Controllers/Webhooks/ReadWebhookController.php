<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReadWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $resource = json_decode($request->getContent());

        Log::info($request->getContent());

        return response()->json(["message" => "ok"]);
    }
}
