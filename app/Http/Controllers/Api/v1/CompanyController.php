<?php

namespace App\Http\Controllers\Api\v1;
use App\Http\Actions\GetInternalId;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\PaginationResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company = Company::select('*')->with('businessIndustry');
        if ($request->input('sort_by') &&  $request->input('sort_by_order')) {
            $company->orderBy($request->input('sort_by'), $request->input('sort_by_order'));
        }
        if ($search = $request->input('search')) {
            $company->where(function ($query) use ($search) {
                $query->where('internal_company_id', 'LIKE', "%$search%")
                    ->orWhere('name', 'LIKE', "%$search%")
                    ->orWhere('furigana_name', 'LIKE', "%$search%");
            });
        }
        $company->where('status', '=', $request->input('showActive'));
        $company = $request->input('paginate')
            ? $company->paginate($request->input('paginate', 25))
            : $company->get();
        return $this->sendResponse(
            ['companies' => CompanyResource::collection($company)],
            $request->input('paginate')
                ? ['paginate' => new PaginationResource($company)]
                : []
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyRequest $request)
    {
        try {
            $company = new Company();
            if ($request->logo_img) {
                $path = config('constants.company_images_path');
                $path = $path . Str::random(10);
                Storage::disk('s3')->put($path, $request->logo_img);
                $company->logo_img = $path;
            }
            $company->name = $request->name;
            $company->furigana_name = $request->furigana_name;
            $company->business_industry_id = $request->business_industry_id;
            $company->office_address = $request->office_address;
            $company->office_phone = $request->office_phone;
            $company->office_email1 = $request->office_email1;
            $company->office_email2 = $request->office_email2;
            $company->office_email3 = $request->office_email3;
            $company->website_url = $request->website_url;
            $company->client_liason = $request->client_liason;
            $company->admin_memo = $request->admin_memo;
            $company->status = $request->status;
            $company->save();
            $company->internal_company_id = GetInternalId::get_internal_company_id($company->id);
            // $company->internal_company_id = $request->internal_company_id;
            if ($company->save()) {
                return $this->sendResponse([
                    'message' => __('messages.record_created_successfully'),
                    'data' => $company
                ]);
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Companies  $Companies
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        //  show a Company from Companies model
        try {
            return $this->sendResponse(new CompanyResource($company));
        } catch (\Exception $e) {
            return $this->sendError('Company not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Companyes  $Companyes
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request, Company $company)
    {
        try {
            if ($company) {
                // lets update the file
                if (Storage::disk('s3')->exists($company->logo_img)) {
                    try {
                        Storage::disk('s3')->delete($company->logo_img);
                    }catch (\Exception $e) {
                        $company->logo_img = null;
                    }
                   
                }
                if ($request->logo_img) {
                    $path = config('constants.company_images_path');
                    $company->logo_img = $path . Str::random(10);
                    Storage::disk('s3')->put($company->logo_img, $request->logo_img);
                } else {
                    $company->logo_img = null;
                }
                $company->internal_company_id = $request->internal_company_id;
                $company->name = $request->name;
                $company->furigana_name = $request->furigana_name;
                $company->business_industry_id = $request->business_industry_id;
                $company->office_address = $request->office_address;
                $company->office_phone = $request->office_phone;
                $company->office_email1 = $request->office_email1;
                $company->office_email2 = $request->office_email2;
                $company->office_email3 = $request->office_email3;
                $company->website_url = $request->website_url;
                $company->client_liason = $request->client_liason;
                $company->admin_memo = $request->admin_memo;
                $company->status = $request->status;
                $company->save();
                return $this->sendResponse([
                    'message' => 'Company Updated',
                    'data' => $company
                ]);
            }
        } catch (\Exception $e) {
            return $this->sendError('failed to update');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Companies  $Companyes
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);
            if ($company->logo_img && Storage::disk('s3')->exists($company->logo_img)) {
                Storage::disk('s3')->delete($company->logo_img);
            }
            $company->delete();
            return $this->sendResponse(['message' => 'Company deleted']);
        } catch (\Exception $e) {
            return $this->sendError('Company not found');
        }
    }
}
