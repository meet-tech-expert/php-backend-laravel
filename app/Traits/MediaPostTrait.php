<?php

namespace App\Traits;

use App\Models\MediaPosts;
use Carbon\Carbon;

trait MediaPostTrait
{
    public function getPopularMediaPostByLastWeek($limit = 3, $isLastWeek = true)
    {
        $lastWeekDate = Carbon::now()->subWeek(1)->format('Y-m-d');

        $media = MediaPosts::query()
            ->with(['mediaViews' => function ($query) use ($lastWeekDate, $isLastWeek)  {
                if ($isLastWeek) {
                    $query->whereDate('created_at', '>=', $lastWeekDate);
                }
            }])
            ->whereHas('mediaViews', function($query) use ($lastWeekDate, $isLastWeek)  {
                if ($isLastWeek) {
                    $query->whereDate('created_at', '>=', $lastWeekDate);
                }
            })
            ->withCount(['mediaViews as media_views_count'=> function($query) use ($lastWeekDate, $isLastWeek)  {
                if ($isLastWeek) {
                    $query->whereDate('created_at', '>=', $lastWeekDate);
                }
            }])
            ->orderBy('media_views_count', 'desc')
            ->limit($limit);

        return $media->get();
    }
}
