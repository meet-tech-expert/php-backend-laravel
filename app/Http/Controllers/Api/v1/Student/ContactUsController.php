<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactUsRequest;
use App\Mail\UserEmailContactUs;
use App\Mail\AdminEmailContactUs;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function store(ContactUsRequest $request)
    {
        try {
            $adminUser = config('constants.kt_admin_email');

            $requestedData = $request->validated();

            Mail::to($requestedData['email'])->send(new UserEmailContactUs($requestedData, config('from.name')));

            Mail::to($adminUser)->send(new AdminEmailContactUs($requestedData));

            return $this->sendResponse([
                'message' => __('Thank you for contacting us.'),
            ]);
        } catch (\Exception $e) {
           return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
