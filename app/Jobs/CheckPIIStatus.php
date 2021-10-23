<?php

namespace App\Jobs;

use App\Enums\PIIStatus;
use App\Models\ResourceFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckPIIStatus implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $limit = 30; // how many files to check at a time.

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        // 1. Fetch the files that need to be processed by 10 at a time.
        // 2. Send the files (using presigned urls that expire in 1 day) for processing and get the id store in DB.
        // 3.

        $this->files = ResourceFile::where('pii_check_status', PIIStatus::PENDING)
            ->orderBy('id', 'asc')
            ->limit(10)
            ->get();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
