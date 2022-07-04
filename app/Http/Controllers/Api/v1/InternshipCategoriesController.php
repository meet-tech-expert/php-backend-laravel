<?php

namespace App\Http\Controllers\Api\v1;
use Illuminate\Routing\Controller as BaseController;

use App\Models\InternshipCategories;
use Illuminate\Http\Request;

class InternshipCategoriesController extends BaseController
{
   /**
     * InternshipCategories List.
     * @group InternshipCategories
     *
     * @param Request $request
     * @queryParam limit integer optional Data Per Page Limit. Example : 10
     *
     * @return \Illuminate\Http\Response
     * @response 200 {"status":"Success","message":"Student List","code":200,"data":{"current_page":1,"data":[{"id":1,"family_name":"Rayhan","first_name":"nbxgcjzbtafuiu","family_name_furigana":"ktllbobqzviftazunfozppr","first_name_furigana":"rlyenpaddbu","email_valid":"ariane.corwin@example.net","email_invalid":"herzog.joy@example.org","is_email_approved":1,"education_facility_id":1,"graduate_year":null,"graduate_month":null,"self_introduction":"culpa","status":null,"created_at":"2022-03-10T16:30:14.000000Z","updated_at":"2022-03-10T16:30:14.000000Z","deleted_at":null}],"first_page_url":"http://localhost:8000/api/students?page=1","from":1,"last_page":1,"last_page_url":"http://localhost:8000/api/students?page=1","links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"http://localhost:8000/api/students?page=1","label":"1","active":true},{"url":null,"label":"Next &raquo;","active":false}],"next_page_url":null,"path":"http://localhost:8000/api/students","per_page":"15","prev_page_url":null,"to":1,"total":1}}
    */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InternshipCategories  $internshipCategories
     * @return \Illuminate\Http\Response
     */
    public function show(InternshipCategories $internshipCategories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InternshipCategories  $internshipCategories
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InternshipCategories $internshipCategories)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InternshipCategories  $internshipCategories
     * @return \Illuminate\Http\Response
     */
    public function destroy(InternshipCategories $internshipCategories)
    {
        //
    }
}
