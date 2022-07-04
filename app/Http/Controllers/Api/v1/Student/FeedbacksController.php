<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackRequest;
use App\Models\Feedbacks;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeedbacksController extends Controller
{


    public function index()
    {
        $authUser = Auth::guard('students')->user();
        if (Feedbacks::where('student_id', $authUser->id)->exists()) {
            $reviews = $this->generateRawSelectSql(["super_power_review", "growth_idea_review"]);
            $feedbacks = Feedbacks::select(DB::raw($reviews))
                ->where('student_id', $authUser->id)
                ->where('is_draft_or_public', 1)
                ->first();
            [$latestSuperPowerFeedback,  $latestGrowthFeedBack] = collect($feedbacks->toArray())
                ->partition(function ($value, $key) {
                    return str_contains($key, 'super_power');
                })
                ->pipe(function ($collection) use ($authUser) {
                    $result = collect();
                    $collection->each(function ($item, $key) use ($result, $authUser) {
                        $collectionKey = $key ? 'growth_idea_review' : 'super_power_review';
                        //  get the maximum
                        $temp = $item->max();
                        // filter the collection having max value
                        $item->filter(function ($value) use ($temp) {
                            return $value ===  $temp;
                        })->pipe(function ($filteredCollection) use ($authUser, $collectionKey, $result) {
                            // check if their max duplicates
                            if ($filteredCollection->count() > 1) {
                                // get their review ids
                                $ids = collect();
                                $filteredCollection->each(function ($val, $k) use ($ids) {
                                    $temp = explode("_", $k);
                                    $ids->push((int) end($temp));
                                });
                                $latestFeedback = Feedbacks::select('id', $collectionKey, 'created_at')
                                    ->where('student_id', $authUser->id)
                                    ->where('is_draft_or_public', 1)
                                    ->whereIn($collectionKey, $ids->toArray())
                                    ->orderByRaw('posted_month DESC, created_at DESC')
                                    ->first();
                                $result->push($latestFeedback);
                            } else {
                                $result->push(null);
                            }
                        });
                    });
                    return $result;
                });
            $companies = Feedbacks::select('company_id')->with('companies')
                        ->where('student_id', $authUser->id)->where('is_draft_or_public', 1)
                        ->distinct()
                        ->orderByRaw('posted_month DESC, created_at DESC')
                        ->get();
            $comments = [];
            foreach ($companies as $company) {
                $comment = Feedbacks::select('super_power_comment', 'is_read', 'growth_idea_comment', 'posted_month', 'id', 'super_power_review', 'growth_idea_review')
                    ->where('company_id', $company->company_id)
                    ->where('student_id', $authUser->id)
                    ->where('is_draft_or_public', 1)
                    ->orderByRaw('posted_month DESC, created_at DESC')
                    ->get();
                $temp = ['company_info' => $company->companies, 'comments' => $comment];
                array_push($comments, $temp);
            }
            return $this->sendResponse([
                'feedbacks' => $feedbacks,
                'comments' => $comments,
                'latest_super_power_feedback' => $latestSuperPowerFeedback,
                'latest_growth_feedback' => $latestGrowthFeedBack
            ]);
        } else {
            return $this->sendResponse([
                'feedbacks' => null,
                'comments' => null,
                'dates' => null,
                'latest_super_power_feedback' => null,
                'latest_growth_feedback' => null
            ]);
        }
    }

    public function generateRawSelectSql($columnNames)
    {
        $reviews = config("constants.reviews_option");
        $statement = [];
        foreach ($columnNames as $columnName) {
            foreach ($reviews as $element) {
                array_push($statement, "SUM(IF({$columnName} = {$element['id']}, 1, 0)) AS {$columnName}_{$element['id']}");
            }
        }
        return implode(",", $statement);
    }

    public function massUpdate(FeedbackRequest $request)
    {
        $data = $request->validated();
        try {
            $authUser = Auth::guard('students')->user();
            $authUser->feedbacks()->where('is_read', 0)->where('is_draft_or_public', 1)->update(['is_read' => 1]);
            return $this->sendResponse([
                'message' => 'record updated'
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }
}
