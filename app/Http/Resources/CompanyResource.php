<?php
namespace App\Http\Resources;

use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CompanyResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $response = parent::toArray($request);

        $response['logo_img'] = isset($this->logo_img) && $this->logo_img != '' && Storage::disk('s3')->exists($this->logo_img) ? Storage::disk('s3')->get($this->logo_img) : null;
        return $response;
    }
}
