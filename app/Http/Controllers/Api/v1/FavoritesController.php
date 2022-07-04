<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoritesResource;
use Illuminate\Http\Request;
use App\Models\Favorites;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\FavoritesRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $authUser = Auth::guard('students')->user();
        $favourites = Favorites::with([
            'internship.company',
            'internship.workCategory',
            'internship.internshipFeaturePosts',
            'internship' => function($query) {
                $query->withCount(['favorites', 'applications']);
            }
              ])
        ->whereHas('internship', function(Builder $query) {
            $query->where('draft_or_public',1);
        })
        ->where('student_id',$authUser->id)->get();
        try {

            return $this->sendResponse([
                'message' => __('messages.show_all_success'),
                'data' => FavoritesResource::collection($favourites),
            ]);

        } catch (\Throwable$th) {
           return $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FavoritesRequest $request)
    {
        try {
            #NOTE: Validate input request
            $requestedData = $request->validated();
            $favourites = new Favorites();
            $favourites->student_id = $requestedData['student_id'];
            $favourites->internship_post_id = $requestedData['internship_post_id'];
            $favourites->status = $requestedData['status'];
            $favourites->save();

            return $this->sendResponse([
                'message' => __('messages.record_created_successfully'),
                'data' => FavoritesResource::collection($favourites),
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Favourites  $favourites
     * @return \Illuminate\Http\Response
     */
    public function show(Request $id)
    {
        try {
            $favourites = Favorites::find($id);

            if ($favourites) {
                return $this->sendResponse([
                    'message' => __('messages.data_found'),
                    'data' => new FavoritesResource($favourites),
                ]);
            }

            return $this->sendError(__('messages.data_not_found'));

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Favourites  $favourites
     * @return \Illuminate\Http\Response
     */
    public function edit(Favorites $favourites)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Favourites  $favourites
     * @return \Illuminate\Http\Response
     */
    public function update(FavoritesRequest $request, $id)
    {
        try {
            #NOTE: Validate input request
            $requestedData = $request->validated();
            $favourites = Favorites::find($id);
            $favourites->student_id = $requestedData['student_id'];
            $favourites->internship_post_id = $requestedData['internship_post_id'];
            $favourites->status = $requestedData['status'];
            $favourites->save();

            return $this->sendResponse([
                'message' => __('messages.record_updated_successfully'),
                'data' => FavoritesResource::collection($favourites),
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Favourites  $favourites
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $id)
    {
        try {
            // Delete the logged-in admin
            Favorites::find($id)->delete();

            return $this->sendResponse([
                'message' => __('messages.deleted_success'),
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }
}
