<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\InternshipPostResource;
use App\Http\Resources\MediaPostResource;
use App\Traits\ApplicationTrait;
use App\Traits\InternshipPostTrait;
use App\Traits\MediaPostTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use InternshipPostTrait;
    use MediaPostTrait;
    use ApplicationTrait;

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $id)
    {
        // try {

            $startDate = Carbon::now()->subMonth()->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');

            /**
             * Show only hiring status(0) internship posts.
             */
            $request->request->add(['status' => 'N']);

            $internshipPosts = $this->getInternshipPostListing($request, null, 5);
            $lastWeekMediaPosts = $this->getPopularMediaPostByLastWeek();
            $allPerioMediaPosts = $this->getPopularMediaPostByLastWeek(3, false);

            return $this->sendResponse([
                'internship_posts' => InternshipPostResource::collection($internshipPosts),
                'ranking_intern_posts' => $this->dashboardPostRanks($startDate, $endDate),
                'popular_media_posts' => MediaPostResource::collection($lastWeekMediaPosts),
                'popular_all_media_posts' => MediaPostResource::collection($allPerioMediaPosts),
                'application_graph_data' => $this->getApplicationsGraphData($startDate, $endDate),
            ]);
        // } catch (\Throwable$th) {
        //     $this->sendApiLogsAndShowMessage($th);
        // }
    }
}
