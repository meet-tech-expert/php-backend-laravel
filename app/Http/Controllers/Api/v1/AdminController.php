<?php

namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    public function login(AdminRequest $request)
    {
        try {
            #NOTE: Validate input request
            $requestedData = $request->validated();

            $admin = Admin::where('email', '=', request('email'))->where('status',1)->first();

            if ($admin && Hash::check($requestedData['password'], $admin->password)) {
                $token = $admin->createToken('admin')->plainTextToken;

                return $this->sendResponse([
                    'token' => $token,
                    'admin' => new AdminResource($admin),
                ]);
            }

            return $this->sendError(__('message.invalid_email_password'), 401);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    public function logout(Request $request) {
        $user = Admin::find(auth()->user()->id);
        // foreach ($user->tokens as $token) {
        //     Log::debug($token);
        // }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // All admin data
            $admins = Admin::all();

            return $this->sendResponse([
               AdminResource::collection($admins),
                'message' => __('messages.show_all_success'),
                
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    public function store(AdminRequest $request)
    {
        try {
            #NOTE: Validate input request
            $requestedData = $request->validated();

            $admin = new Admin();
            $admin->name = $requestedData['name'];
            $admin->email = $requestedData['email'];
            $admin->status = 1;
            $admin->password = Hash::make($requestedData['password']);
            $admin->save();

            return $this->sendResponse([
                'message' => __('messages.record_created_successfully'),
                'data' => new AdminResource($admin),
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    public function show($id)
    {
        try {
            $admin = Admin::find($id);

            if ($admin) {
                return $this->sendResponse([
                    new AdminResource($admin),
                    'message' => __('messages.data_found'),   
                ]);
            }

            return $this->sendError(__('messages.data_not_found'));

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }
    public function update(AdminRequest $request, $id)
    {
        try {
            #NOTE: Validate input request
            $requestedData = $request->validated();

            $admin = Admin::find($id);

            $admin->name = $requestedData['name'];
            $admin->email = $requestedData['email'];
            $admin->status = $requestedData['status'];
            $admin->save();

            return $this->sendResponse([
                'message' => __('messages.update_success'),
                'data' => new AdminResource($admin),
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }


    function emailCheck(AdminRequest $request )
    {
  
     $requestedData = $request->validated();
      $email = $requestedData['email'];
      $data = Admin::where('email', $email)
              ->count();
      if($data > 0){
        return $this->sendResponse([
            'message' => __('messages.not available'),
        ]);
      }
      else{
        return $this->sendResponse([
            'message' => __('messages.This_e_mail_address_is_not_registered'),
        ]);
      }
     
    }

    public function destroy($id)
    {
        try {
            // Delete the logged-in admin
            Admin::find($id)->delete();

            return $this->sendResponse([
                'message' => __('messages.deleted_success'),
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }
}
