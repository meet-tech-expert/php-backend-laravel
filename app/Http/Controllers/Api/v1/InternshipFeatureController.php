<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\InternshipFeatureResource;
use App\Models\InternshipFeatures;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\InternshipFeatureRequest;
use App\Http\Resources\PaginationResource;
use Illuminate\Support\Facades\DB;
use App\Models\InternshipFeaturePost;
class InternshipFeatureController extends  Controller
{


    /**
     * InternshipFeatures List.
     * @group InternshipFeatures
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @response 200 {"data":{"message":"messages.show_all_success","data":[{"id":1,"name":"demo","slug":"demo-slug","created_at":"2022-03-18T09:53:57.000000Z","updated_at":"2022-03-18T09:53:57.000000Z","deleted_at":null},{"id":2,"name":"demo1","slug":"demo-slug1","created_at":"2022-03-18T09:54:19.000000Z","updated_at":"2022-03-18T09:54:19.000000Z","deleted_at":null}]}}
     */
    public function index(InternshipFeatureRequest $request)
    {

        $requestedData = $request->validated();

        $internshipFeatures = InternshipFeatures::select('*');
        if ($requestedData['sort_by'] &&  $requestedData['sort_by_order']) {
            $internshipFeatures->orderBy(DB::raw('ISNULL(display_order), display_order'), 'ASC');
        }

        if ( isset($requestedData['search'])) {
            $search = $requestedData['search'];
            $internshipFeatures->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            });
        }
        $internshipFeatures =  $requestedData['paginate']
            ? $internshipFeatures->paginate( $requestedData['paginate'], 25)
            : $internshipFeatures->get();
        return $this->sendResponse(
            ['internship_features' => InternshipFeatureResource::collection($internshipFeatures)],
            $requestedData['paginate']
                ? ['paginate' => new PaginationResource($internshipFeatures)]
                : []
        );

    }

    /**
     * InternshipFeatures Create.
     * @group InternshipFeatures
     * @param Request $request
     * @bodyParam name string Example: demo name
     * @bodyParam slug year Example: demo-slug
     * @return \Illuminate\Http\Response
     * @response 200 {"data":{"message":"messages.record_created_successfully","data":{"name":"demo","slug":"demo-slug","updated_at":"2022-03-18T09:53:57.000000Z","created_at":"2022-03-18T09:53:57.000000Z","id":1}}}
     */

    public function store(InternshipFeatureRequest $request)
    {
        try {

            $requestedData = $request->only('name', 'display_order');

            $internshipFeature = new InternshipFeatures();
            $internshipFeature->name = $requestedData['name'];
            $internshipFeature->display_order = $requestedData['display_order'];
            $internshipFeature->save();

            return $this->sendResponse([
                'message' => __('messages.record_created_successfully'),
                'data' => new InternshipFeatureResource($internshipFeature),
            ]);

        }  catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

      /**
     * InternshipFeatures View.
     * @group InternshipFeatures
     * @urlParam id integer required Example: 1
     * @return \Illuminate\Http\Response
     * @response 200 {"message":"The record exists.","data":{"id":52,"name":"rereeeeee","slug":"erererwerwerew","created_at":"2022-03-17T06:05:27.000000Z","updated_at":"2022-03-17T06:05:27.000000Z","deleted_at":null}}
     * */

    public function show($id)
    {
        try {
            $internshipFeature = InternshipFeatures::find($id);

            if ($internshipFeature) {
                return $this->sendResponse([
                    'message' => __('messages.data_found'),
                    'data' => new InternshipFeatureResource($internshipFeature),
                ]);
            }

            return $this->sendError(__('messages.data_not_found'));

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * InternshipFeatures Update.
     * @group InternshipFeatures
     * @param Request $request
     * @urlParam id integer required Example: 1
    * @bodyParam name string Example: demo name
     * @bodyParam slug year Example: demo-slug
     * @return \Illuminate\Http\Response
     * @response 200 {"data":{"message":"messages.update_success","data":{"id":1,"name":"change","slug":"chage-demo","created_at":"2022-03-18T09:53:57.000000Z","updated_at":"2022-03-18T09:55:20.000000Z","deleted_at":null}}}
     *
     **/
    public function update(InternshipFeatureRequest $request, $id)
    {
        try {
        
            
            $requestedData = $request->validated();
            $feature = InternshipFeatures::find($id);
            if ($requestedData['name']) {
                $feature->name = $requestedData['name'];
            }
            
            if ($requestedData['display_order']) {
                $feature->display_order = $requestedData['display_order'];
            }

            $feature->save();
            return $this->sendResponse([
                'message' => __('messages.update_success'),
                'data' => new InternshipFeatureResource($feature),
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

     /**
     * InternshipFeatures Delete.
     * @group InternshipFeatures
     * @urlParam id integer required Example: 1
     * @return \Illuminate\Http\Response
     * @response 200 {"data":{"message":"messages.deleted_success"}}
     * */

    public function destroy($id)
    {
        try {
            //Checked if feature is assigned to Intership Post
            $internshipFeaturePost = InternshipFeaturePost::where('internship_feature_id',$id)->count();
            if($internshipFeaturePost >= 1){
                return $this->sendError(__('messages.assiged_to_intership'));
            }

            $feature = InternshipFeatures::find($id);
            $feature->display_order = null;
            $feature->name = $feature->name . '_deleted' . $feature->id;
            $feature->save();
            $feature->delete();
            
            return $this->sendResponse([
                'status' => 200,
                'message' => __('messages.deleted_success'),
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }


}
