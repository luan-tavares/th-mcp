<?php

namespace App\Jobs;

use App\Support\GoogleDriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UploadGoogleDriveJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private GoogleDriveService $googleDriveService) {}


    public function handle(): void
    {
        $this->googleDriveService->createTranscriptFile();
    }
}
