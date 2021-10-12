<?php

namespace App\Jobs;

use App\Enums\PIIStatus;
use App\Models\ResourceFile;
use App\Models\ResourceThumbnail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPIIFileCheck implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->files = ResourceFile::where('pii_check', PIIStatus::PENDING)
            ->orderBy('id', 'asc')
            ->limit(20)
            ->get();

        $this->thumbnails = ResourceThumbnail::where('pii_check', PIIStatus::PENDING)
            ->orderBy('id', 'asc')
            ->limit(20)
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
