<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\EducationFacilitiesResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\EducationFacilitiesRequest;
use Illuminate\Support\Facades\Log;
use App\Models\EducationFacilities;
use Illuminate\Http\Request;
use App\Http\Resources\PaginationResource;

class EducationFacilitiesController extends Controller
{


    /**
     * EducationFacilities List.
     * @group EducationFacilities
     * @param Request $request
     * @queryParam limit integer optional Data Per Page Limit. Example : 10
     * @return \Illuminate\Http\Response
     * @response 200 {"data":{"message":"messages.show_all_success","data":[{"id":1,"name":"sdfds","type":0,"created_at":null,"updated_at":null,"deleted_at":null},{"id":2,"name":"Celine Borer","type":1,"created_at":"2022-03-17T17:17:38.000000Z","updated_at":"2022-03-17T17:17:38.000000Z","deleted_at":null},{"id":3,"name":"Alexander Steuber","type":1,"created_at":"2022-03-17T17:17:38.000000Z","updated_at":"2022-03-17T17:17:38.000000Z","deleted_at":null},{"id":4,"name":"London Murray","type":1,"created_at":"2022-03-17T17:17:38.000000Z","updated_at":"2022-03-17T17:17:38.000000Z","deleted_at":null},{"id":5,"name":"Jasen Haag V","type":1,"created_at":"2022-03-17T17:17:38.000000Z","updated_at":"2022-03-17T17:17:38.000000Z","deleted_at":null},{"id":6,"name":"Florencio Gerlach","type":1,"created_at":"2022-03-17T17:17:39.000000Z","updated_at":"2022-03-17T17:17:39.000000Z","deleted_at":null},{"id":7,"name":"Lori Carter","type":1,"created_at":"2022-03-17T17:17:39.000000Z","updated_at":"2022-03-17T17:17:39.000000Z","deleted_at":null},{"id":8,"name":"Felicity Eichmann","type":1,"created_at":"2022-03-17T17:17:39.000000Z","updated_at":"2022-03-17T17:17:39.000000Z","deleted_at":null},{"id":9,"name":"Gay Emmerich","type":1,"created_at":"2022-03-17T17:17:39.000000Z","updated_at":"2022-03-17T17:17:39.000000Z","deleted_at":null},{"id":10,"name":"Nelda Lehner","type":1,"created_at":"2022-03-17T17:17:39.000000Z","updated_at":"2022-03-17T17:17:39.000000Z","deleted_at":null},{"id":11,"name":"Prof. Kevin Senger","type":1,"created_at":"2022-03-17T17:17:39.000000Z","updated_at":"2022-03-17T17:17:39.000000Z","deleted_at":null}]}}
     */

    public function index(Request $request)
    {

        $facilities = EducationFacilities::select('*');
        if ($request->input('sort_by') &&  $request->input('sort_by_order')) {
            $facilities->orderBy($request->input('sort_by'), $request->input('sort_by_order'));
        }

        if ($request->input('search')) {
            $search = $request->input('search');
            $facilities->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            });
        }
        $facilities = $request->input('paginate')
            ? $facilities->paginate($request->input('paginate', 25))
            : $facilities->get();
        return $this->sendResponse(
            ['education_facilities' => EducationFacilitiesResource::collection($facilities)],
            $request->input('paginate')
                ? ['paginate' => new PaginationResource($facilities)]
                : []
        );
    }



    public function create()
    {
        $EducationFacility = new EducationFacilities();
        return $EducationFacility;
    }



    /**
     * EducationFacilities Create.
     * @group EducationFacilities
     * @param Request $request
     * @bodyParam name string Example: demo name
     * @bodyParam type number Example:1
     * @return \Illuminate\Http\Response
     * @response 200 {"data":{"message":"messages.record_created_successfully","data":{"name":"asdsadas","type":"1","updated_at":"2022-03-18T03:15:43.000000Z","created_at":"2022-03-18T03:15:43.000000Z","id":12}}}
     */
    public function store(EducationFacilitiesRequest $request)
    {
        try {

            $requestedData = $request->validated();

            $workingCategory = new EducationFacilities();
            $workingCategory->name = $requestedData['name'];
            $workingCategory->type = $requestedData['type'];
            $workingCategory->save();

            return $this->sendResponse([
                'message' => __('messages.record_created_successfully'),
                'data' => new EducationFacilitiesResource($workingCategory),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * EducationFacilities View.
     * @group EducationFacilities
     * @urlParam id integer required Example: 1
     * @return \Illuminate\Http\Response
     * @response 200 {"data":{"message":"messages.data_found","data":{"id":1,"name":"sdfds","type":0,"created_at":null,"updated_at":null,"deleted_at":null}}}
     * */

    public function show($id)
    {
        try {
            $educationFacility = EducationFacilities::find($id);

            if ($educationFacility) { 
                return $this->sendResponse([
                    'message' => __('messages.data_found'),
                    'data' => new EducationFacilitiesResource($educationFacility),
                ]);
            }

            return $this->sendError(__('messages.data_not_found'));
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }


    /**
     * EducationFacilities Update.
     * @group EducationFacilities
     * @param Request $request
     * @urlParam id integer required Example: 1
     * @bodyParam name string Example: demo name
     * @bodyParam slug year Example: demo-slug
     * @return \Illuminate\Http\Response
     * @response 200 {"message":"The record has been updated successfully.","data":{"id":52,"name":"demo","slug":"demo-slug","created_at":"2022-03-17T06:05:27.000000Z","updated_at":"2022-03-17T07:21:33.000000Z","deleted_at":null}}
     *
     **/
    public function update(EducationFacilitiesRequest $request, $id)
    {
        try {
            #NOTE: Validate input request
            $requestedData = $request->validated();
            $EducationFacility = EducationFacilities::find($id);
            $EducationFacility->name = $requestedData['name'];
            $EducationFacility->type = $requestedData['type'];
            $EducationFacility->save();

            return $this->sendResponse([
                'message' => __('messages.update_success'),
                'data' => new EducationFacilitiesResource($EducationFacility),
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }
    /**
     * EducationFacilities Delete.
     * @group EducationFacilities
     * @urlParam id integer required Example: 1
     * @return \Illuminate\Http\Response
     * @response 200 {"data":{"message":"messages.deleted_success"}}
     * */

    public function destroy($id)
    {
        try {
            EducationFacilities::find($id)->delete();
            return $this->sendResponse([
                'status' => 200,
                'message' => __('messages.deleted_success'),
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }
}
