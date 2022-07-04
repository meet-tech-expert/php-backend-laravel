<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ApplicationsResource extends JsonResource
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
        if (isset($response['internship_post'])) {
            $internship = $response['internship_post'];
            $internship['period_value'] = collect(config('constants.period'))->where('id', $internship['period'])->first() ?? '-';
            $internship['workload_value'] = collect(config('constants.workload'))->where('id', $internship['workload'])->first() ?? '-';
            $internship['target_grade_value'] = collect(config('constants.target_grade'))->where('id', $internship['target_grade'])->first() ?? '-';
            $internship['wage_value'] = collect(config('constants.wage'))->where('id', $internship['wage'])->first() ?? '-';
            $internship['seo_featured_image'] = Storage::disk('s3')->exists($internship['seo_featured_image']) ? Storage::disk('s3')->url($internship['seo_featured_image']) : null;
            $internship['seo_featured_image_thumbnail'] = Storage::disk('s3')->exists($internship['seo_featured_image_thumbnail']) ? Storage::disk('s3')->url($internship['seo_featured_image_thumbnail']) : null;
            $internship['company'] = $this->whenLoaded('company', new CompanyResource($this->company));
            $response['internship_post'] = $internship;
        }
        $response['student']['obfuscate_email'] = isset($response['student']['email_valid']) && $response['student']['email_valid'] != '' ? $this->obfuscateEmail($response['student']['email_valid']) : '';
        return $response;
    }
    private function obfuscateEmail($input, $show = 3)
    {
        $arr = explode('@', $input);

        $email = substr($arr[0], 0, $show) . str_repeat('*', 5);
        $host = substr($arr[1], 0, $show) . str_repeat('*', 5);

        return $email . '@' . $host;
    }
}
