<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Send success response.
     *
     * @param array $data success data
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($data = [], $metaData = [], $status = 200)
    {
        $response = [
            'data' => $data,
        ];
        foreach ($metaData  as $key => $value) {
            $response[$key] = $value;
        }
        return response()->json($response, $status);
    }

    /**
     * Send error response.
     *
     * @param string $message error message
     * @param int $code http response code
     * @return \Illuminate\Http\Response
     */
    public function sendError($message, $code = 400)
    {
        return response()->json(['message' => $message], $code);
    }

    /**
     * Send error response.
     *
     * @param string $message error message
     * @param int $code http response code
     * @return \Illuminate\Http\Response
     */
    public function sendApiLogsAndShowMessage($e, $code = 503)
    {
        if (config('app.env') != 'production') {
            return $e;
            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'action' => request()->route()->getAction()
            ], 503);
        }

        $authUser = auth()->user();
        Log::info([
            'API_RETURN_STATUS_CODE' => $code,
            'USER_ID' => isset($authUser->id) ? $authUser->id : null,
            'URI' => request()->getUri(),
            'METHOD' => request()->getMethod(),
            'ACTION' => request()->route()->getAction(),
            'REQUEST_BODY' => request()->all(),
            'ERROR_MESSAGE' => $e->getMessage(),
        ]);
        return response()->json(['message' => __('messages.something_goes_wrong')], $code);
    }
}
