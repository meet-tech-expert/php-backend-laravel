<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\MediaPostRequest;
use App\Http\Resources\MediaPostResource;
use App\Http\Resources\PaginationResource;
use App\Models\MediaPosts;
use App\Models\MediaPostView;
use Illuminate\Database\Eloquent\Builder;

class MediaPostsController extends Controller
{
    public function index(MediaPostRequest $request)
    {
        try {
            $application = MediaPosts::query();
            $application = $application->withCount(['mediaViews as media_views_count']);
            if ($request->input('tag_search') && count(json_decode($request->input('tag_search'))) > 0) {
                $tagFilters = json_decode($request->input('tag_search'));
                $application = $application->with('mediaTags')->whereHas('mediaTags', function (Builder $query) use ($tagFilters) {
                    $query->whereIn('media_tag_id', $tagFilters);
                });
            } else {
                $application = $application->with('mediaTags');
            }
            if ($request->input('sort_by') === 'view_counts') {
                $application = $application->orderByRaw("media_views_count DESC");
            }
            if ($request->input('sort_by') === 'display_order') {
                $application = $application->orderByRaw("-display_order DESC");
            }
            if ($request->input('sort_by') && $request->input('sort_by') !== 'view_counts' && $request->input('sort_by') !== 'display_order') {
                $application = $application->orderBy($request->input('sort_by'), $request->input('sort_by_order', 'desc'));
            }

            $application = $application->when($request->input('is_draft'), function ($query, $isDraft) {
                $query->where('is_draft', $isDraft == 'Y' ? '1' : '0');
            })
            ->addSelect("id", "title", "seo_slug", "seo_featured_image_thumbnail", "public_date");
            $application = $application->paginate($request->input('paginate', 25));
            $data = MediaPostResource::collection($application);
            $paginate = new PaginationResource($application);
            return $this->sendResponse([
                'data' => $data,
                'paginate' => $paginate,
                'counts' => [
                    'total_opened' => MediaPosts::where('is_draft', 0)->count(),
                    'total_drafted' => MediaPosts::where('is_draft', 1)->count(),
                ],
            ]);
        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    public function show($id)
    {
        try {
            $mediaPost = MediaPosts::withCount(['mediaViews as media_views_count'])->with(['mediaTags:id,name'])
                ->where(function ($query) use ($id) {
                    $query->where('id', $id);
                    $query->orWhere('seo_slug', $id);
                })->first();
            $mediaPostView = new MediaPostView();
            $mediaPost->mediaViews()->save($mediaPostView);

            return $this->sendResponse([
                'message' => __('messages.show_all_success'),
                'data' => new MediaPostResource($mediaPost),
            ]);
        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    public function recommendation(MediaPostRequest $request)
    {
        // get the authenticated data
        $requestData = $request->validated();
        $tags = $requestData['tags'];
        $tagSearch = MediaPosts::query()->with('mediaTags')
            ->whereHas('mediaTags', function ($query) use ($tags) {
                $query->whereIn('media_tag_id', $tags);
            })
            ->addSelect("id", "title", "seo_slug", "seo_featured_image_thumbnail", "public_date")
            ->where('id', '!=', $requestData['post_id'])
            ->where('is_draft', '=', 0);

        if ($tagSearch->exists()) {
            $tagSearch = $tagSearch->latest('public_date')->paginate(3);
            return $this->sendResponse([
                'recommendations' => MediaPostResource::collection($tagSearch),
            ]);
        }

        $posts = MediaPosts::with('mediaTags')
            ->where('id', '!=', $requestData['post_id'])
            ->where('is_draft', '=', 0)
            ->latest('public_date')
            ->addSelect("id", "title", "seo_slug", "seo_featured_image_thumbnail", "public_date")
            ->paginate(3);

        return $this->sendResponse([
            'recommendations' => MediaPostResource::collection($posts),
        ]);
    }

}
