<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Redis;

class AdvertisementViewCounter
{
    protected const DIRTY_SET = 'ad:views:dirty';

    public function increment(int $advertisementId): void
    {
        Redis::incr($this->viewKey($advertisementId));
        Redis::sadd(self::DIRTY_SET, $advertisementId);
    }

    public function pendingCount(int $advertisementId): int
    {
        return (int) Redis::get($this->viewKey($advertisementId));
    }

    public function totalViews(int $mysqlView, int $advertisementId): int
    {
        return $mysqlView + $this->pendingCount($advertisementId);
    }

    public function dirtyAdvertisementIds(): array
    {
        return array_map('intval', Redis::smembers(self::DIRTY_SET));
    }

    public function takePendingCount(int $advertisementId): int
    {
        $key = $this->viewKey($advertisementId);
        $count = (int) Redis::get($key);

        if ($count <= 0) {
            return 0;
        }

        Redis::del($key);
        Redis::srem(self::DIRTY_SET, $advertisementId);

        return $count;
    }

    protected function viewKey(int $advertisementId): string
    {
        return "ad:views:{$advertisementId}";
    }
}
