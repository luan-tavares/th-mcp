<?php

namespace App\Console\Commands;

use App\Jobs\UploadGoogleDriveJob;
use App\Support\GoogleDriveService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CreateFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $content = Storage::get('teste.json');


        UploadGoogleDriveJob::dispatch($content);
    }
}
