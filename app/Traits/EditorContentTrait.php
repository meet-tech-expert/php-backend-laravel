<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait EditorContentTrait
{
    private function updateContentImages($content, $updatePost = null, $imagePath = null)
    {
        if (!$updatePost || !$content || !$imagePath) {
            return null;
        }

        $fileDir = $imagePath . $updatePost->id . '/';
        // /**
        //  * Delete Old Images
        //  */
        // if (Storage::disk('s3')->exists($fileDir)) {
        //     Storage::disk('s3')->delete($fileDir);
        // }

        $dom = new \DOMDocument();
        $dom->loadHtml('<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        // foreach  in the submited message
        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            // if the img source is 'data-url'
            if (preg_match('/data:image/', $src)) {
                $fileName = uniqid();

                $extension = explode('/', explode(':', substr($src, 0, strpos($src, ';')))[1])[1];   // .jpg .png .pdf

                $replace = substr($src, 0, strpos($src, ',')+1);

                $image = str_replace($replace, '', $src);

                $image = str_replace(' ', '+', $image);

                $filepath = $fileDir . $fileName.'.'.$extension;

                Storage::disk('s3')->put($filepath, base64_decode($image));

                $newPath = Storage::disk('s3')->url($filepath);

                $img->removeAttribute('src');
                $img->setAttribute('src', $newPath);
            }

            if ($style = $img->getAttribute('style')) {
                $img->setAttribute('style', $style);
            }
        }
        $html = $dom->saveHTML();
        return str_replace('<?xml encoding="utf-8" ?>', '', $html);
    }
}
