<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MediaPostRequest;
use App\Http\Resources\MediaPostResource;
use App\Http\Resources\PaginationResource;
use App\Models\MediaPosts;
use App\Traits\EditorContentTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageUploadService;

class MediaPostsController extends Controller
{
    use EditorContentTrait;

    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    /**
     * Media Posts List.
     * @group Media Posts
     *
     * @param Request $request
     * @queryParam limit integer optional Data Per Page Limit. Example : 10
     *
     * @return \Illuminate\Http\Response
     * @response 200 {"status":"Success","message":"Media Posts List","code":200,"data":{"current_page":1,"data":[{"id":1,"title":"Post Title","summery":"Post Summery","media_tag_id":1,"seo_slug":"media-slug","seo_ogp":"media-seo-ogp","seo_featured_image":"media-seo_featured_image","description":"media-description","is_draft":1,"page_views":1,"status":1,"created_at":"2022-03-14T08:08:23.000000Z","updated_at":"2022-03-14T08:08:23.000000Z","deleted_at":null}],"first_page_url":"http://localhost:8000/api/media-posts?page=1","from":1,"last_page":1,"last_page_url":"http://localhost:8000/api/media-posts?page=1","links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"http://localhost:8000/api/media-posts?page=1","label":"1","active":true},{"url":null,"label":"Next &raquo;","active":false}],"next_page_url":null,"path":"http://localhost:8000/api/media-posts","per_page":"5","prev_page_url":null,"to":1,"total":1}}
     */
    public function index(MediaPostRequest $request)
    {
        try {
            $application = MediaPosts::withCount(['mediaViews as media_views_count'])
                ->with(['mediaTags'])
                ->orderBy($request->input('sort_by'), $request->input('sort_by_order', 'desc'))
                ->when($request->input('is_draft'), function ($query, $isDraft) {
                    $query->where('is_draft', $isDraft == 'Y' ? '1' : '0');
                })
                ->when($request->input('search'), function ($query, $search) {
                    $query->where('title', 'LIKE', "%$search%");
                });

            $application = $application->paginate($request->input('paginate', 25));

            return $this->sendResponse([
                'data' => MediaPostResource::collection($application),
                'paginate' => new PaginationResource($application),
                'counts' => [
                    'total_opened' => MediaPosts::where('is_draft', 0)->count(),
                    'total_drafted' => MediaPosts::where('is_draft', 1)->count(),
                ],
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Media Posts Create.
     * @group Media Posts
     * @param Request $request
     * @bodyParam title string Example: Post Title
     * @bodyParam summery string Example: Post Summery
     * @bodyParam media_tag_id array Example: Post Media Tag
     * @bodyParam seo_slug string required Example: media-slug
     * @bodyParam seo_ogp string required Example: media-seo-ogp
     * @bodyParam seo_featured_image string required Example: media-seo_featured_image
     * @bodyParam description string required Example: media-description
     * @bodyParam is_draft boolean required Example: 1
     * @bodyParam status boolean required Example: 1
     * @return \Illuminate\Http\Response
     * @response 200 {"status":"Success","message":"Created Successfully","code":201,"data":{"title":"Post Title","summery":"Post Summery","media_tag_id":"1","seo_slug":"media-slug","seo_ogp":"media-seo-ogp","seo_featured_image":"media-seo_featured_image","description":"media-description","is_draft":true,"page_views":"1","status":true,"updated_at":"2022-03-14T08:07:25.000000Z","created_at":"2022-03-14T08:07:25.000000Z","id":5}}
     */

    public function store(MediaPostRequest $request)
    {
        try {

            $mediaPost = new MediaPosts();

            if ($file = $request->file('seo_featured_image')) {
                $path = config('constants.media_images_path');
                $uniqId = uniqid();
                $imageName = $uniqId . '.' . $file->extension();
                $fullPathName = $path . $imageName;
                Storage::disk('s3')->putFileAs($path, $file, $imageName);
                $mediaPost->seo_featured_image = $fullPathName;

                $thumbName = $uniqId . '_thumbnail.' . $file->extension();
                $thumbnailFullPathName = $path . $thumbName;
                $thumbResponse = $this->imageUploadService->getThumbnail($file, $thumbnailFullPathName);

                $mediaPost->seo_featured_image_thumbnail = $thumbnailFullPathName;
                // if ($thumbResponse) {
                // }
            }

            $mediaPost->title = $request->title;
            $mediaPost->summery = $request->summery;
            $mediaPost->seo_slug = $request->seo_slug;
            $mediaPost->seo_ogp = $request->seo_ogp;
            $mediaPost->seo_meta_description = $request->seo_meta_description;
            $mediaPost->display_order = $request->display_order;
            $mediaPost->is_draft = $request->is_draft ?? 0;
            $mediaPost->public_date = $request->is_draft ? null : now();
            $mediaPost->status = 1;

            $mediaPost->save();

            $mediaPost->mediaTags()->sync($request->media_tag_id);

            // Update Content
            $imagePath = config('constants.media_images_content_path');
            $mediaPost->description = $this->updateContentImages($request->description, $mediaPost, $imagePath);
            $mediaPost->save();

            return $this->sendResponse([
                'message' => __('messages.saved_success'),
                'data' => new MediaPostResource($mediaPost),
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Media Posts View.
     * @group Media Posts
     *
     * @urlParam id integer required Example: 1
     *
     * @return \Illuminate\Http\Response
     * @response 200 {"status":"Success","message":"Media Post View","code":200,"data":{"id":1,"title":"Post Title","summery":"Post Summery","media_tag_id":1,"seo_slug":"media-slug","seo_ogp":"media-seo-ogp","seo_featured_image":"media-seo_featured_image","description":"media-description","is_draft":1,"page_views":1,"status":1,"created_at":"2022-03-14T08:08:23.000000Z","updated_at":"2022-03-14T08:08:23.000000Z","deleted_at":null}}
     * */

    public function show($id)
    {
        try {
            $mediaPost = MediaPosts::withCount(['mediaViews as media_views_count'])->with(['mediaTags:id,name'])->findOrFail($id);

            return $this->sendResponse([
                'message' => __('messages.show_all_success'),
                'data' => new MediaPostResource($mediaPost),
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Media Posts Update.
     * @group Media Posts
     *
     * @param Request $request
     *
     * @bodyParam title string Example: Post Title
     * @bodyParam summery string Example: Post Summery
     * @bodyParam media_tag_id string Example: Post Media Tag
     * @bodyParam seo_slug array required Example: media-slug
     * @bodyParam seo_ogp string required Example: media-seo-ogp
     * @bodyParam seo_featured_image string required Example: media-seo_featured_image
     * @bodyParam description string required Example: media-description
     * @bodyParam is_draft boolean required Example: 1
     * @bodyParam status boolean required Example: 1
     *
     * @return \Illuminate\Http\Response
     * @response 200 {"status":"Success","message":"Updated Successfully","code":201,"data":{"student_id":"1","company_id":"1","internship_post_id":"1","updated_at":"2022-03-10T16:30:14.000000Z","created_at":"2022-03-10T16:30:14.000000Z","id":1}}
     */
    public function update($id, MediaPostRequest $request)
    {
        try {
            $mediaPost = MediaPosts::findOrFail($id);

            $mediaPost->title = $request->title;
            $mediaPost->summery = $request->summery;
            $mediaPost->seo_slug = $request->seo_slug;
            $mediaPost->seo_ogp = $request->seo_ogp;
            $mediaPost->seo_meta_description = $request->seo_meta_description;
            $mediaPost->display_order = $request->display_order;
            // 0 for draft
            // 1 for production

            // draft =====>> publish
            if($mediaPost->is_draft == 1 && $request->is_draft == 0) {
                $mediaPost->public_date = now();
            }

            $mediaPost->is_draft = $request->is_draft ?? 0;


            $mediaPost->status = $request->status ?? 0;

            if ($file = $request->file('seo_featured_image')) {
                $path = config('constants.media_images_path');
                $uniqId = uniqid();
                $imageName = $uniqId . '.' . $file->extension();
                $fullPathName = $path . $imageName;
                Storage::disk('s3')->putFileAs($path, $file, $imageName);
                $mediaPost->seo_featured_image = $fullPathName;

                $thumbName = $uniqId . '_thumbnail.' . $file->extension();
                $thumbnailFullPathName = $path . $thumbName;
                $thumbResponse = $this->imageUploadService->getThumbnail($file, $thumbnailFullPathName);

                $mediaPost->seo_featured_image_thumbnail = $thumbnailFullPathName;
                // if ($thumbResponse) {
                // }
            }

            if($request->clear_image == 1) {
                try {
                    if(Storage::disk('s3')->exists($mediaPost->seo_featured_image)) {
                        Storage::disk('s3')->delete($mediaPost->seo_featured_image);
                    }
                }catch (\Exception $e) {
                    $mediaPost->seo_featured_image = null;
                }
            }

            $imagePath = config('constants.media_images_content_path');
            $mediaPost->description = $this->updateContentImages($request->description, $mediaPost, $imagePath);
            $mediaPost->save();

            $mediaPost->mediaTags()->sync($request->media_tag_id);

            return $this->sendResponse([
                'message' => __('messages.update_success'),
                'data' => new MediaPostResource($mediaPost),
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Media Posts Delete.
     * @group Media Posts
     *
     * @urlParam id integer required Example: 1
     *
     * @return \Illuminate\Http\Response
     * @response 200  {"status":"Success","message":"Deleted Successfully","code":200,"data":[]}
     * */

    public function destroy($id)
    {
        try {
            MediaPosts::findOrFail($id)->delete();
            return $this->sendResponse(['message' => 'Post deleted']);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }
}
