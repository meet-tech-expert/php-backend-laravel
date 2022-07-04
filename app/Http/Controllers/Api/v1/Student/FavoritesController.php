<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use App\Models\Favorites;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Favourites  $favourites
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $id)
    {
        try {

            $this->validate($request, [
                'internship_post_id' => 'required',
                'is_favourite' => 'required|boolean',
            ]);

            $favourites = Favorites::where('student_id', auth()->user()->id)
                ->where('internship_post_id', $request->internship_post_id)->first();
            if ($request->is_favourite) {
                if(!$favourites){
                    $favourites = new Favorites();
                    $favourites->student_id = auth()->user()->id;
                    $favourites->internship_post_id = $request->internship_post_id;
                    $favourites->status = 1;
                    $favourites->save();
                }
                $favourited = 1;
            } else {
                $favourites = Favorites::where('student_id', auth()->user()->id)
                ->where('internship_post_id', $request->internship_post_id)->delete();
                $favourited = 0;
            }

            $totalFavouriteCount = Favorites::where('internship_post_id', $request->internship_post_id)->count();

            return $this->sendResponse([
                'message' => __('messages.record_updated_successfully'),
                'total_counts' => $totalFavouriteCount,
                'favourited' => $favourited,
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }
}
