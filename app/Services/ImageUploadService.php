<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Image;

class ImageUploadService
{

    public function getThumbnail($file,$thumbnailFullPathName){
        $thumbFile = Image::make($file)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                        });
        $result = Storage::disk('s3')->put($thumbnailFullPathName, $thumbFile->stream());

        return $result;
    }
}
