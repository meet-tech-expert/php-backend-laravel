<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\WorkingCategoryRequest;
use App\Http\Resources\WorkingCategoryResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaginationResource;
use App\Models\WorkCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\InternshipPosts;
class WorkCategoriesController extends Controller
{

    /**
     * WorkCategory List.
     * @group WorkCategory
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @response 200 {"message":"Successfully fetched all records.","data":[{"id":51,"name":"rere","slug":"ererer","created_at":"2022-03-17T05:30:58.000000Z","updated_at":"2022-03-17T05:30:58.000000Z","deleted_at":null},{"id":52,"name":"rereeeeee","slug":"erererwerwerew","created_at":"2022-03-17T06:05:27.000000Z","updated_at":"2022-03-17T06:05:27.000000Z","deleted_at":null},{"id":54,"name":"minhaz","slug":"minhaz-slug","created_at":"2022-03-17T06:30:59.000000Z","updated_at":"2022-03-17T06:30:59.000000Z","deleted_at":null}]}
     */
    public function index(WorkingCategoryRequest $request)
    {
        $requestedData = $request->validated();

        $workingCategories = WorkCategories::select('*');
        if ($requestedData['sort_by'] &&  $requestedData['sort_by_order']) {
            $workingCategories->orderBy(DB::raw('ISNULL(display_order), display_order'), 'ASC');
        }

        if ( isset($requestedData['search'])) {
            $search = $requestedData['search'];
            $workingCategories->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            });
        }
        $workingCategories =  $requestedData['paginate']
            ? $workingCategories->paginate( $requestedData['paginate'], 25)
            : $workingCategories->get();
        return $this->sendResponse(
            ['working_categories' => WorkingCategoryResource::collection($workingCategories)],
            $requestedData['paginate']
                ? ['paginate' => new PaginationResource($workingCategories)]
                : []
        );
    }

   /**
     * WorkCategory Create.
     * @group WorkCategory
     * @param Request $request
     * @bodyParam name string Example: demo name
     * @bodyParam slug year Example: demo-slug
     * @return \Illuminate\Http\Response
     * @response 200 {"message":"The data has been saved successfully.","data":{"name":"minhaz","slug":"minhaz-slug","updated_at":"2022-03-17T06:30:59.000000Z","created_at":"2022-03-17T06:30:59.000000Z","id":54}}
     */
    public function store(WorkingCategoryRequest $request)
    {

        
        try {

            $requestedData = $request->validated();

            $workingCategory = new WorkCategories();
            $workingCategory->name = $requestedData['name'];
            $workingCategory->display_order = $requestedData['display_order'];
            $workingCategory->save();

            return $this->sendResponse([
                'message' => __('messages.record_created_successfully'),
                'data' => new WorkingCategoryResource($workingCategory),
            ]);

        }  catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * WorkCategory View.
     * @group WorkCategory
     * @urlParam id integer required Example: 1
     * @return \Illuminate\Http\Response
     * @response 200 {"message":"The record exists.","data":{"id":52,"name":"rereeeeee","slug":"erererwerwerew","created_at":"2022-03-17T06:05:27.000000Z","updated_at":"2022-03-17T06:05:27.000000Z","deleted_at":null}}
     * */

    public function show($id)
    {
        try {
            $workingCategory = WorkCategories::find($id);

            if ($workingCategory) {
                return $this->sendResponse([
                    'message' => __('messages.data_found'),
                    'data' => new WorkingCategoryResource($workingCategory),
                ]);
            }

            return $this->sendError(__('messages.data_not_found'));

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }


    /**
     * WorkCategory Update.
     * @group WorkCategory
     * @param Request $request
     * @urlParam id integer required Example: 1
    * @bodyParam name string Example: demo name
     * @bodyParam slug year Example: demo-slug
     * @return \Illuminate\Http\Response
     * @response 200 {"message":"The record has been updated successfully.","data":{"id":52,"name":"demo","slug":"demo-slug","created_at":"2022-03-17T06:05:27.000000Z","updated_at":"2022-03-17T07:21:33.000000Z","deleted_at":null}}
     *
     **/
    public function update(WorkingCategoryRequest $request, $id)
    {
        try {
            
            $requestedData = $request->validated();
            $category = WorkCategories::find($id);

            if ($requestedData['name']) {
                $category->name = $requestedData['name'];
            }
            
            if ($requestedData['display_order']) {
                $category->display_order = $requestedData['display_order'];
            }

            $category->save();

            return $this->sendResponse([
                'message' => __('messages.update_success'),
                'data' => new WorkingCategoryResource($category),
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

      /**
     * WorkCategory Delete.
     * @group WorkCategory
     * @urlParam id integer required Example: 1
     * @return \Illuminate\Http\Response
     * @response 200 {"message":"The record has been deleted successfully."}
     * */


    public function destroy($id)
    {
        try {

            //Checked if category is assigned to Intership Post
            $internshipPosts = InternshipPosts::where('work_category_id',$id)->count();
            if($internshipPosts >= 1){
                return $this->sendError(__('messages.assiged_to_intership'));
            }

            // Delete the logged-in admin
            $workingCategory = WorkCategories::find($id);
            $workingCategory->display_order = null;
            $workingCategory->name = $workingCategory->name . '_deleted'. $workingCategory->id;
            $workingCategory->save();
            $workingCategory->delete();

            return $this->sendResponse([
                'message' => __('messages.deleted_success'),
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }


}
