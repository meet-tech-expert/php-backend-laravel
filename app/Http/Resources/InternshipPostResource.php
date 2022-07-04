<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class InternshipPostResource extends JsonResource
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

        $response['company'] = $this->whenLoaded('company', new CompanyResource($this->company));

        $response['seo_featured_image'] = isset($this->seo_featured_image) && $this->seo_featured_image != '' ? Storage::disk('s3')->url($this->seo_featured_image) : null;
        $response['seo_featured_image_thumbnail'] = isset($this->seo_featured_image_thumbnail) && $this->seo_featured_image_thumbnail != '' ? Storage::disk('s3')->url($this->seo_featured_image_thumbnail) : null;

        $response['period_value'] = collect(config('constants.period'))->where('id', $this->period)->first() ?? '-';
        $response['workload_value'] = collect(config('constants.workload'))->where('id', $this->workload)->first() ?? '-';
        $response['target_grade_value'] = collect(config('constants.target_grade'))->where('id', $this->target_grade)->first() ?? '-';
        $response['wage_value'] = collect(config('constants.wage'))->where('id', $this->wage)->first() ?? '-';

        $response['created_at'] = isset($this->created_at) && $this->created_at != '' ? $this->created_at->format('Y-m-d H:i:s') : null;
        $response['preview_url'] = 'internship-detail/' .
                                    $response['id'] .
                                    '/' .
                                    rawurldecode($response['seo_slug']).
                                    '/?preview=1';
        return $response;
    }
}
