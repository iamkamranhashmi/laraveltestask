<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\FileUploadController;

class DeleteExpiredFiles extends Command
{
    protected $signature = 'files:delete-expired';
    protected $description = 'Delete files older than 24 hours and notify via RabbitMQ';

    public function handle()
    {
        $controller = new FileUploadController();
        $controller->deleteExpiredFiles();
        $this->info('Expired files deleted successfully.');
    }
}
