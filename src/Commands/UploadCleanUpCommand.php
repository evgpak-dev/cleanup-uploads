<?php

namespace Evgpak\UploadCleanUp\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UploadCleanUpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans up the upload folder.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // In a real application, this would check database connections,
        // disk space, external services, etc.
        // For this example, we'll just log a message.

        $this->info('Cleaning up the upload folder...');

        $files = Storage::disk('public')->listContents('uploaded_files');

        $numOfDeletedFiles = collect($files)
            ->filter(function($file) {
                return $file['type'] === 'file' && $file['lastModified'] < now()->subDays(5)->getTimestamp();
            })
            ->each(function($file) {
                Storage::disk('public')->delete($file['path']);
            })->count();

        $this->info("{$numOfDeletedFiles} files have been deleted (".now().").");

        return self::SUCCESS;
    }
}
