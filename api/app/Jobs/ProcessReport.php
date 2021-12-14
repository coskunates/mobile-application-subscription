<?php

namespace App\Jobs;

use App\Caches\DeviceCache;
use App\Models\Report;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProcessReport
 * @package App\Jobs
 */
class ProcessReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var object $event
     */
    protected object $event;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(object $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $device = DeviceCache::get($this->event->getSubscription()->device_id);
        $report = Report::where('application_id', $this->event->getSubscription()->application_id)
            ->where('date', Carbon::now(new DateTimeZone(getenv('LOCAL_TIMEZONE')))->format('Y-m-d'))
            ->where('os', $device['os'])
            ->where('event', $this->event->getName())
            ->limit(1)
            ->get()
            ->first();
        if (empty($report)) {
            $report = new Report();
            $report->application_id = $this->event->getSubscription()->application_id;
            $report->date = Carbon::now(new DateTimeZone(getenv('LOCAL_TIMEZONE')))->format('Y-m-d');
            $report->os = $device['os'];
            $report->event = $this->event->getName();
            $report->count = 1;
        } else {
            $report->count += 1;
        }

        $report->save();
    }
}
