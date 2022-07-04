<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\InternshipPostRequest;
use App\Http\Resources\InternshipPostResource;
use App\Http\Resources\PaginationResource;
use App\Models\InternshipPosts;
use App\Traits\EditorContentTrait;
use App\Traits\InternshipPostTrait;
use Illuminate\Support\Facades\Auth;

class InternshipPostsController extends Controller
{
    use InternshipPostTrait;
    use EditorContentTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InternshipPostRequest $request)
    {
        try {

            $authUser = Auth::guard('students')->user();
            $request->merge(['draft_or_public' => 'public']);
            $posts = $this->getInternshipPostListing($request, $authUser); // Default Order;

            return $this->sendResponse([
                'data' => InternshipPostResource::collection($posts),
                'paginate' => new PaginationResource($posts),
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InternshipPosts  $internshipPosts
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $request = request();
            $authUser = Auth::guard('students')->user();

            $post = InternshipPosts::with([
                'internshipFeaturePosts:id,name',
                'workCategory:id,name',
                'company:id,name,logo_img,business_industry_id',
                'company.businessIndustry:id,name',
                'applications' => function ($query) use ($authUser) {
                    $query->where('student_id', $authUser->id ?? 0);
                },
            ])->withCount([
                'applications',
                'favorites',
                'favorites as is_favourite' => function ($query) use ($authUser) {
                    $query->where('student_id', $authUser->id ?? 0);
                },
                'applications as is_applied_applications' => function ($query) use ($authUser) {
                    $query->where('student_id', $authUser->id ?? 0);
                },
            ])
                ->whereIn('draft_or_public', $request->get('preview') ? ['0', '1'] : ['1'])
                ->where('id', $id)
                ->where(function ($where) use ($request) {
                    $where->where('title', $request->get('title'));
                    $where->orWhere('seo_slug', $request->get('title'));
                })
                ->first();

            if (!$post) {
                return $this->sendResponse([
                    'message' => __('messages.data_not_found'),
                ], [], 404);
            }

            return $this->sendResponse([
                'message' => __('messages.show_all_success'),
                'data' => new InternshipPostResource($post),
            ]);
        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

}
