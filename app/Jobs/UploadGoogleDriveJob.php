<?php

namespace App\Jobs;

use App\Support\GoogleDriveService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class UploadGoogleDriveJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private string $raw) {}


    public function handle(): void
    {
        $googleDriveService = new GoogleDriveService($this->raw);
        $googleDriveService->createTranscriptFile();
    }
}
