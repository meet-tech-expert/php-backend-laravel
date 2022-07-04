<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Actions\GetInternalId;
use App\Http\Controllers\Controller;
use App\Http\Requests\InternshipPostRequest;
use App\Http\Resources\InternshipPostResource;
use App\Http\Resources\PaginationResource;
use App\Models\Applications;
use App\Models\InternshipPosts;
use App\Models\Favorites;
use App\Models\InternshipFeaturePost;
use App\Services\ImageUploadService;
use App\Traits\EditorContentTrait;
use App\Traits\InternshipPostTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InternshipPostsController extends Controller
{
    use InternshipPostTrait;
    use EditorContentTrait;

    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InternshipPostRequest $request)
    {
        try {
            $posts = $this->getInternshipPostListing($request);

            return $this->sendResponse([
                'message' => __('messages.show_all_success'),
                'data' => InternshipPostResource::collection($posts),
                'paginate' => new PaginationResource($posts),
                'counts' => [
                    'total' => InternshipPosts::where('draft_or_public', 1)->count(),
                    'total_opened' => InternshipPosts::where('status', 0)->where('draft_or_public', $request->draft_or_public == 'draft' ? 0 : 1)->count(),
                    'total_ended' => InternshipPosts::where('status', 1)->where('draft_or_public', $request->draft_or_public == 'draft' ? 0 : 1)->count(),
                    'total_draft' => InternshipPosts::where('draft_or_public', 0)->count(),
                ],
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InternshipPostRequest $request)
    {
        try {
            $post = new InternshipPosts();
            $post->title = $request->title;
            $post->company_id = $request->company_id;
            $post->work_category_id = $request->work_category_id;
            $post->period = $request->period;
            $post->workload = $request->workload;
            $post->wage = $request->wage;
            $post->target_grade = $request->target_grade;
            $post->application_step_1 = $request->application_step_1;
            $post->application_step_2 = $request->application_step_2;
            $post->application_step_3 = $request->application_step_3;
            $post->application_step_4 = $request->application_step_4;
            $post->seo_slug = $request->seo_slug;
            $post->seo_ogp = $request->seo_ogp;
            $post->seo_meta_description = $request->seo_meta_description;
            $post->draft_or_public = $request->draft_or_public ?? 0;
            $post->display_order = $request->display_order ?? null;
            $post->status = 0;
            if ($request->draft_or_public == '1') {
                $post->public_date = now();
            }

            if ($request->file('seo_featured_image') && $request->remove_image == 0) {
                $file = $request->file('seo_featured_image');
                $path = config('constants.internship_images_path');

                $uniqId = uniqid();
                $imageName = $uniqId . '.' . $file->extension();
                $fullPathName = $path . $imageName;

                Storage::disk('s3')->putFileAs($path, $file, $imageName);
                $post->seo_featured_image = $fullPathName;

                $thumbName = $uniqId . '_thumbnail.' . $file->extension();
                $thumbnailFullPathName = $path . $thumbName;
                $thumbResponse = $this->imageUploadService->getThumbnail($file, $thumbnailFullPathName);

                if ($thumbResponse) {
                    $post->seo_featured_image_thumbnail = $thumbnailFullPathName;
                }
            }

            $post->save();

            $post->internshipFeaturePosts()->sync($request->internship_feature_id);
            /**
             * After saving the post, get the post ID to generate rand ID.
             */
            $post->internal_internship_id = GetInternalId::get_internal_internship_post_id($post->id, $post->company_id);
            $imagePath = config('constants.internship_images_content_path');
            $post->description_corporate_profile = $this->updateContentImages($request->description_corporate_profile, $post, $imagePath);
            $post->description_internship_content = $this->updateContentImages($request->description_internship_content, $post, $imagePath);
            $post->save();

            return $this->sendResponse([
                'message' => __('messages.saved_success'),
                'data' => new InternshipPostResource($post), // required for UI
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
            $post = InternshipPosts::with(['internshipFeaturePosts:id,name'])->withCount(['applications', 'favorites'])->findOrFail($id);

            return $this->sendResponse([
                'message' => __('messages.show_all_success'),
                'data' => new InternshipPostResource($post),
            ]);
        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InternshipPosts  $internshipPosts
     * @return \Illuminate\Http\Response
     */
    public function update(InternshipPostRequest $request, $id)
    {
        $imagePath = config('constants.internship_images_content_path');
        $post = InternshipPosts::findOrFail($id);

        try {
            $post->title = $request->title;
            $post->company_id = $request->company_id;
            $post->work_category_id = $request->work_category_id;
            $post->period = $request->period;
            $post->workload = $request->workload;
            $post->wage = $request->wage;
            $post->target_grade = $request->target_grade;
            $post->application_step_1 = $request->application_step_1;
            $post->application_step_2 = $request->application_step_2;
            $post->application_step_3 = $request->application_step_3;
            $post->application_step_4 = $request->application_step_4;
            $post->seo_slug = $request->seo_slug;
            $post->seo_ogp = $request->seo_ogp;
            $post->seo_meta_description = $request->seo_meta_description;
            $post->description_corporate_profile = $this->updateContentImages($request->description_corporate_profile, $post, $imagePath);
            $post->description_internship_content = $this->updateContentImages($request->description_internship_content, $post, $imagePath);
            $draft_status = $post->draft_or_public;
            $post_status = $post->status;
            $post->draft_or_public = $request->draft_or_public ?? 0;
            $post->display_order = $request->display_order ?? null;
            $post->status = $request->status ?? 0;
            if (is_null($post->public_date) && $request->draft_or_public == '1') {
                $post->public_date = now();
            } elseif (!is_null($post->public_date) && $draft_status == '0' && $request->draft_or_public == '1') {
                $post->public_date = now();
            } elseif (!is_null($post->public_date) && $post_status == '1' && $request->status == '0') {
                $post->public_date = now();
            }

            if ($file = $request->file('seo_featured_image')) {
                // lets update the file
                if (Storage::disk('s3')->exists($post->seo_featured_image)) {
                    Storage::disk('s3')->delete($post->seo_featured_image);
                }

                if (Storage::disk('s3')->exists($post->seo_featured_image_thumbnail)) {
                    Storage::disk('s3')->delete($post->seo_featured_image_thumbnail);
                }
                $path = config('constants.internship_images_path');

                $uniqId = uniqid();
                $imageName = $uniqId . '.' . $file->extension();
                $fullPathName = $path . $imageName;

                Storage::disk('s3')->putFileAs($path, $file, $imageName);
                $post->seo_featured_image = $fullPathName;

                $thumbName = $uniqId . '_thumbnail.' . $file->extension();
                $thumbnailFullPathName = $path . $thumbName;
                $thumbResponse = $this->imageUploadService->getThumbnail($file, $thumbnailFullPathName);
                if ($thumbResponse) {
                    $post->seo_featured_image_thumbnail = $thumbnailFullPathName;
                }
            }

            if ($request->remove_image == 1) {
                if (Storage::disk('s3')->exists($post->seo_featured_image)) {
                    Storage::disk('s3')->delete($post->seo_featured_image);
                }

                if (Storage::disk('s3')->exists($post->seo_featured_image_thumbnail)) {
                    Storage::disk('s3')->delete($post->seo_featured_image_thumbnail);
                }
                $post->seo_featured_image = "";
                $post->seo_featured_image_thumbnail = "";
            }

            $post->save();

            $post->internshipFeaturePosts()->sync($request->internship_feature_id);

            return $this->sendResponse([
                'message' => __('messages.update_success'),
                'data' => new InternshipPostResource($post), // required for UI
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InternshipPosts  $internshipPosts
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            InternshipPosts::findOrFail($id)->delete();
            Applications::where('internship_post_id', $id)->delete();
			InternshipFeaturePost::where('internship_post_id', $id)->delete();
			Favorites::where('internship_post_id', $id)->delete();
            return $this->sendResponse(['message' => 'Post deleted']);
        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

}
