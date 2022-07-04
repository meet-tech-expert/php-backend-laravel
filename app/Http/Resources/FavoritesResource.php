<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FavoritesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $response = parent::toArray($request);
        $response['is_favourited'] = 1;
        if(isset($response['internship'])) {
            $response['internship'] = new InternshipPostResource($this->internship);
            // $internship = $response['internship'];
            // $internship['period_value'] = collect(config('constants.period'))->where('id', $internship['period'])->first() ?? '-';
            // $internship['workload_value'] = collect(config('constants.workload'))->where('id', $internship['workload'])->first() ?? '-';
            // $internship['target_grade_value'] = collect(config('constants.target_grade'))->where('id', $internship['target_grade'])->first() ?? '-';
            // $internship['wage_value'] = collect(config('constants.wage'))->where('id', $internship['wage'])->first() ?? '-';
            // $internship['seo_featured_image'] =  $internship['seo_featured_image'] && 
            //                                     $internship['seo_featured_image'] != '' && 
            //                                     Storage::disk('s3')->exists($internship['seo_featured_image']) 
            //                                     ? Storage::disk('s3')->url($internship['seo_featured_image']) : null;
            // $internship['seo_featured_image_thumbnail'] = $internship['seo_featured_image_thumbnail'] &&
            //                                               $internship['seo_featured_image_thumbnail'] != '' &&
            //                                              Storage::disk('s3')->exists($internship['seo_featured_image_thumbnail'])
            //                                               ? Storage::disk('s3')->url($internship['seo_featured_image_thumbnail']):'';
            // if(isset($internship['company']) && $internship['company']['logo_img'] && Storage::disk('s3')->exists($internship['company']['logo_img'])) {
            //     $internship['company']['logo_img'] = Storage::disk('s3')->get($internship['company']['logo_img']);
            // }
            // $response['internship'] = $internship;
        }
        return $response;
    }
}
