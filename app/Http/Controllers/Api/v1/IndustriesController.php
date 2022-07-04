<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\IndustryResource;
use App\Models\BusinessIndustries;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class IndustriesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BusinessIndustries::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $industry = new BusinessIndustries();
            $industry->name = $request->name;
            $industry->slug = $request->slug;
            $industry->save();
        }
        catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json($industry, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Industies  $industies
     * @return \Illuminate\Http\Response
     */
    public function show(Request $id)
    {
        try {
            return BusinessIndustries::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['message' => 'industry not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Industies  $industies
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Industies  $industies
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $industry = new BusinessIndustries();


        try {
            #NOTE: Validate input request
            $requestedData = $request->validated();

            $industry = BusinessIndustries::find($id);

            $industry->name = $requestedData['name'];
            $industry->slug = $requestedData['slug'];
            $industry->save();

            return $this->sendResponse([
                'message' => __('messages.update_success'),
                'data' => new IndustryResource($industry),
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Industies  $industies
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $id)
    {
        try {
            BusinessIndustries::findOrFail($id)->delete();
            return response()->json(['message' => 'Company deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Company not found'], 404);
        }
    }
}
