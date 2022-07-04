<?php

namespace App\Http\Controllers\Api\v1\Student;

use Illuminate\Http\Request;
Use App\Traits\InternshipPostTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\InternshipPostResource;
use App\Http\Resources\MediaPostResource;
use App\Models\MediaPosts;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    use InternshipPostTrait;

    public function index(Request $request) {
        $request->merge(['draft_or_public' => 'public']);
        $authUser = Auth::guard('students')->user();
        $internshipPosts = $this->getInternshipPostListing($request, $authUser);
        $mediaPosts = MediaPosts::select('id', 'title', 'status', 'is_draft', 'seo_featured_image_thumbnail' ,'created_at', 'updated_at', 'public_date')->with('mediaTags')->where('is_draft', '0')->orderBy('updated_at', 'DESC')->paginate(6);
        return $this->sendResponse([
            'internship_posts' => InternshipPostResource::collection($internshipPosts),
            'media_posts' => MediaPostResource::collection($mediaPosts) 
        ]);
    }
}
