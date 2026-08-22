<?php

namespace App\Console\Commands;

use App\Http\Services\AdvertisementViewCounter;
use App\Models\Advertisement\Advertisement;
use Illuminate\Console\Command;

class FlushAdvertisementViewsCommand extends Command
{
    protected $signature = 'ads:flush-views';

    protected $description = 'Flush buffered advertisement view counts from Redis to MySQL';

    public function handle(AdvertisementViewCounter $counter): int
    {
        $ids = $counter->dirtyAdvertisementIds();

        if (empty($ids)) {
            return self::SUCCESS;
        }

        $flushed = 0;

        foreach ($ids as $id) {
            $pending = $counter->takePendingCount($id);

            if ($pending <= 0) {
                continue;
            }

            Advertisement::whereKey($id)->increment('view', $pending);
            $flushed++;
        }

        $this->info("Flushed views for {$flushed} advertisement(s).");

        return self::SUCCESS;
    }
}
