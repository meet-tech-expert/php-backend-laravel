<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackRequest;
use App\Http\Resources\FeedbackResource;
use App\Http\Resources\PaginationResource;
use App\Mail\FeedbackMail;
use App\Models\Feedbacks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
class FeedbacksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $feedback = Feedbacks::with(
                'student.educationFacility:id,name',
                'student:id,student_internal_id,education_facility_id,family_name,first_name',
                'companies:id,name,internal_company_id'
                )
                ->when($request->is_draft_or_public, function ($query, $isDraftOrPublic) {
                    $query->where('is_draft_or_public', $isDraftOrPublic == 'Y' ? '0' : '1');
                })
                ->when($request->search, function ($query, $search) {
                    $query->whereHas('student', function ($where) use ($search) {
                        $where->where('first_name', 'LIKE', "%{$search}%");
                        $where->orWhere('student_internal_id', 'LIKE', "%{$search}%");
                        $where->orWhere('family_name', 'LIKE', "%{$search}%");
                        $where->orWhere('family_name_furigana', 'LIKE', "%{$search}%");
                        $where->orWhere('first_name_furigana', 'LIKE', "%{$search}%");
                    });
                    $query->orWhereHas('companies', function ($where) use ($search) {
                        $where->where('name', 'LIKE', "%{$search}%");
                        $where->orWhere('internal_company_id', 'LIKE', "%{$search}%");
                        $where->orWhere('furigana_name', 'LIKE', "%{$search}%");
                    });
                })
                ->when($request->input('sort_by'), function ($query, $sortBy) use ($request) {
                    $query->orderBy($sortBy, $request->sort_by_order);
                })
                ->when($request->input('posted_month_start'), function ($query, $postedMonthStart) use ($request) {
                    $query->where('posted_month', '>=', $postedMonthStart);
                    $query->where('posted_month', '<=', $request->posted_month_end);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($request->input('paginate', 25));

            return $this->sendResponse([
                'message' => __('messages.show_all_success'),
                'data' => FeedbackResource::collection($feedback),
                'counts' => [
                    'drafts' => Feedbacks::where('is_draft_or_public', 0)->count(),
                    'public' => Feedbacks::where('is_draft_or_public', 1)->count(),
                ],
                'paginate' => new PaginationResource($feedback),
            ]);
        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FeedbackRequest $request)
    {
        try {
            $feedback = new Feedbacks();
            $feedback->student_id = $request->student_id;
            $feedback->company_id = $request->company_id;
            $feedback->super_power_review = $request->super_power_review;
            $feedback->super_power_comment = $request->super_power_comment;
            $feedback->growth_idea_review = $request->growth_idea_review;
            $feedback->growth_idea_comment = $request->growth_idea_comment;
            $feedback->is_draft_or_public = $request->is_draft_or_public;
            $feedback->is_read = 0;
            $feedback->posted_month = date('Y-m', strtotime($request->posted_month));
            $feedback->created_at = now();
            $feedback->updated_at = now();
            $feedback->save();
            $feedback->load('student', 'companies');
            Mail::to($feedback->student->email_valid)->send(new FeedbackMail($feedback));

            return $this->sendResponse([
                'message' => __('messages.success'),
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Feedbacks  $feedbacks
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $feedback = Feedbacks::with([
                'student:id,student_internal_id,family_name,first_name',
                'companies:id,name,internal_company_id',
            ])
                ->find($id);

            if (!$feedback) {
                return $this->sendError(__('messages.data_not_found'));
            }

            return $this->sendResponse([
                'message' => __('messages.data_found'),
                'data' => new FeedbackResource($feedback),
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Feedbacks  $feedbacks
     * @return \Illuminate\Http\Response
     */
    public function update(FeedbackRequest $request, $id)
    {
        try {
            $feedback = Feedbacks::find($id);
            $feedback->student_id = $request->student_id;
            $feedback->company_id = $request->company_id;
            $feedback->super_power_review = $request->super_power_review;
            $feedback->super_power_comment = $request->super_power_comment;
            $feedback->growth_idea_review = $request->growth_idea_review;
            $feedback->growth_idea_comment = $request->growth_idea_comment;
            $feedback->is_draft_or_public = $request->is_draft_or_public;
            $feedback->is_read = 0;
            $feedback->posted_month = date('Y-m', strtotime($request->posted_month));
            $feedback->updated_at = now();
            $feedback->save();

            return $this->sendResponse([
                'message' => __('messages.success'),
            ]);

        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Feedbacks  $feedbacks
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Feedbacks::where('id', $id)->delete();
            return $this->sendResponse([
                'message' => __('messages.success'),
            ]);
        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }
}
