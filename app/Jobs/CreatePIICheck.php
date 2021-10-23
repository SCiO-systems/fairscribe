<?php

namespace App\Jobs;

use App\Models\ResourceFile;
use App\Utilities\SCIO\TokenGenerator;
use Http;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Storage;

class CreatePIICheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The resource file instance.
     */
    protected $resourceFile;

    /**
     * The base URI of the service.
     */
    protected $baseURI;

    /**
     * The timeout of the http requests.
     */
    protected $requestTimeout;

    /**
     * The token to use for making requests to the service.
     */
    protected $token;

    /**
     * The presigned urls ttl.
     */
    protected $presignedTtl;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ResourceFile $resourceFile)
    {
        $generator = new TokenGenerator();
        $this->token = $generator->getToken();
        $this->resourceFile = $resourceFile;
        $this->baseURI = env('SCIO_SERVICES_BASE_API_URL');
        $this->requestTimeout = env('REQUEST_TIMEOUT_SECONDS');
        $this->presignedTtl = env('PRESIGNED_URL_TTL_IN_HOURS', 24);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 1. Get the user of the resource file.
        // 2. Generate the pre-signed url.
        // 3. Send the details for the job.
        // 4. Get the response if it is successful and update the pii check id of the resource file.

        $user = $this->resourceFile->user;
        $path = $this->resourceFile->path;

        if (empty($user)) {
            Log::error('Failed to find a user for the resource file', [
                'resource_file' => $this->resourceFile->id,
                'job' => get_class($this),
            ]);
            return;
        }

        $email = $user->email;
        $presignedUrl = Storage::temporaryUrl($path, now()->addHours($this->presignedTtl));

        $response = Http::timeout(env('REQUEST_TIMEOUT_SECONDS'))
            ->acceptJson()
            ->asJson()
            ->withToken($this->token)
            ->post("$this->baseURI/pii/submitjob", [
                "email" => $email,
                "path" => $presignedUrl,
                "title" => $this->resourceFile->filename,
                "mode" => "recall",
                "language" => "en"
            ]);

        if ($response->failed()) {
            Log::error('Failed to get response for PII check system.', [
                'response_status' => $response->status(),
                'response_error' => $response->json(),
                'job' => get_class($this),
            ]);
            return;
        }

        Log::info('success', ['response' => $response->json()]);
    }
}
