<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\UploadGoogleDriveJob;
use App\Support\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReadWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $content = $request->getContent();

        Log::info($content);

        UploadGoogleDriveJob::dispatch($content)->onQueue('th-readme-create-transcript');

        return response()->json(["message" => "ok"]);
    }
}
