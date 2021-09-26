<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\YoutubeDL\Interfaces\YoutubeDLInterface;
use App\Models\ConvertRequestItem;
use Exception;

class YoutubeDL implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 900; // 15 minutes timeout
    protected $convertRequestItem;
    protected $saveInPath;
    protected $formatIds;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ConvertRequestItem $convertRequestItem, $saveInPath, $formatIds)
    {
        $this->connection = 'redis';
        $this->queue = env('QUEUE_YOUTUBE_DL', 'youtube-dl');
        $this->convertRequestItem = $convertRequestItem;
        $this->saveInPath = $saveInPath;
        $this->formatIds = $formatIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $details = $this->convertRequestItem['details'];

        try {
            $youtubeDLService = app(YoutubeDLInterface::class);
            $this->convertRequestItem->load('convertRequest');
            $url = $this->convertRequestItem->convertRequest->url;

            $response = $youtubeDLService->downloadYoutube($url, $this->saveInPath, $this->formatIds);

            $this->convertRequestItem->update([
                'status' => ConvertRequestItem::STATUS_CONVERTED,
                'details' => $details,
            ]);

        } catch (Exception $e) {
            $details['error_msg'] = $e->getMessage();

            $this->convertRequestItem->update([
                'status' => ConvertRequestItem::STATUS_JOB_ERROR_BAD_REQUEST,
                'details' => $details
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Exception $e)
    {
        $details = $this->convertRequestItem['details'];
        $details['error_msg'] = 'Job timeout';
        $this->convertRequestItem->update([
            'status' => ConvertRequestItem::STATUS_JOB_ERROR_TIMEOUT,
            'details' => $details
        ]);
    }
}
