<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MediaTagsRequest;
use App\Http\Resources\MediaTagsResource;
use App\Http\Resources\PaginationResource;
use App\Models\MediaTags;

class MediaTagsController extends Controller
{
  
    public function index(MediaTagsRequest $request)
    {
        $requestedData = $request->validated();
        $mediaTags = MediaTags::select('*');

        if ($requestedData['sort_by'] &&  $requestedData['sort_by_order']) {
            $mediaTags->orderBy($requestedData['sort_by'], $requestedData['sort_by_order']);
        }

        if (isset($requestedData['search'])) {
            $search = $requestedData['search'];
            $mediaTags->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            });
        }
        $mediaTags =  $requestedData['paginate']
            ? $mediaTags->paginate($requestedData['paginate'] ?? 25)
            : $mediaTags->get();
        return $this->sendResponse(
            ['media_tags' => MediaTagsResource::collection($mediaTags)],
            $requestedData['paginate']
                ? ['paginate' => new PaginationResource($mediaTags)]
                : []
        );
    }

  
    public function store(MediaTagsRequest $request)
    {
        try {

            $requestedData = $request->validated();
            $mediaTag = new MediaTags();
            $mediaTag->name = $requestedData['name'];
            $mediaTag->save();

            return $this->sendResponse([
                'message' => __('messages.record_created_successfully'),
                'data' => new MediaTagsResource($mediaTag),
            ]);

        }  catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MediaTags  $mediaTags
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $tag = MediaTags::find($id);

            if ($tag) {
                return $this->sendResponse([
                    'message' => __('messages.data_found'),
                    'data' => $tag,
                ]);
            }

            return $this->sendError(__('messages.data_not_found'));
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MediaTags  $mediaTags
     * @return \Illuminate\Http\Response
     */
    public function update(MediaTagsRequest $request, $id)
    {
            $requestedData = $request->validated();
            #NOTE: Validate input request
         try {
            $mediaTag = MediaTags::find($id);
            $mediaTag->name = $requestedData['name'];
            $mediaTag->save();
            return $this->sendResponse([
                'message' => __('messages.update_success'),
                'data' => new MediaTagsResource($mediaTag),
            ]);

         } catch (\Throwable$th) {
             return $this->sendApiLogsAndShowMessage($th);
         }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MediaTags  $mediaTags
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Delete the logged-in admin
            MediaTags::find($id)->delete();

            return $this->sendResponse([
                'message' => __('messages.deleted_success'),
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }
}
