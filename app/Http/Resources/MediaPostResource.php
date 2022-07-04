<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MediaPostResource extends JsonResource
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

        $response['seo_featured_image'] = isset($this->seo_featured_image) && $this->seo_featured_image != '' ? Storage::disk('s3')->url($this->seo_featured_image) : null;

        $response['seo_featured_image_thumbnail'] = isset($this->seo_featured_image_thumbnail) && $this->seo_featured_image_thumbnail != '' ? Storage::disk('s3')->url($this->seo_featured_image_thumbnail) : null;

        return $response;
    }
}
