<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'current_page' => $this->currentPage(),
            'total_pages' => $this->lastPage(),
            'records_from' => $this->firstItem() ?? 0,
            'records_to' => $this->lastItem() ?? 0,
            'records_total' => $this->total(),
            
        ];
    }
}
