<?php

namespace App\Traits;

use App\Models\InternshipPosts;
use Illuminate\Support\Facades\DB;

trait InternshipPostTrait
{
    private function getInternshipPostListing($request, $authUser = null, $limit = 0)
    {
        $posts = InternshipPosts::query()
            ->with(['company:id,name,logo_img,business_industry_id', 'workCategory:id,name', 'company.businessIndustry:id,name'])
            ->withCount([
                'applications',
                'favorites',
                'favorites as is_favourited' => function ($query) use ($authUser) {
                    $query->where('student_id', $authUser->id ?? 0);
                },
            ])
            ->addSelect("id", "title", "company_id", "work_category_id", "period", "workload", "seo_slug", "seo_featured_image_thumbnail", "wage", "target_grade")
            ->when($request->input('status'), function ($query, $status) {
                if ($status == 'Y') {
                    $query->where('status', '1');
                } else if ($status == 'N') {
                    $query->where('status', '0');
                } else {
                    $query->whereIn('status', ['0', '1']);
                }
            })
            ->when($request->input('draft_or_public'), function ($query, $draftOrPublic) {
                $query->where('draft_or_public', $draftOrPublic == 'draft' ? '0' : '1');
            })
            ->when($request->input('search'), function ($query, $search) {
                if (is_numeric($search)) {
                    $query->where('id', $search);
                } else {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'LIKE', "%$search%");
                        $q->orWhere('internal_internship_id', 'LIKE', "%$search%");
                        $q->orWhereHas('company', function ($t) use ($search) {
                            $t->where('name', 'LIKE', "%$search%");
                        });
                    });
                }
            })
            ->when($request->input('date_from'), function ($query) use ($request) {
                $query->whereBetween('created_at', [$request->input('date_from') . ' 00:00:00', $request->input('date_to') . ' 23:59:59']);
            })
            ->when($request->input('work_category_ids'), function ($query, $workCategoryIds) {
                if (is_array($workCategoryIds)) {
                    return $query->whereIn('work_category_id', $workCategoryIds);
                }

                return $query->where('work_category_id', $workCategoryIds);
            })
            ->when($request->input('business_industry_ids'), function ($query, $businessIndustoryIds) {
                $query->whereHas('company', function ($query) use ($businessIndustoryIds) {
                    $query->whereHas('businessIndustry', function ($where) use ($businessIndustoryIds) {
                        if (is_array($businessIndustoryIds)) {
                            return $where->whereIn('business_industries.id', $businessIndustoryIds);
                        }

                        return $where->where('business_industries.id', $businessIndustoryIds);
                    });
                });
            })
            ->when($request->input('internship_feature_list'), function ($query, $internshipFeatureList) {
                $query->whereHas('internshipFeaturePosts', function ($query) use ($internshipFeatureList) {
                    $query->whereIn('internship_features.id', $internshipFeatureList);
                });
            })
            ->when($request->input('period'), function ($query, $period) {
                $query->whereIn('period', $period);
            })
            ->when($request->input('workload'), function ($query, $workload) {
                $query->whereIn('workload', $workload);
            })
            ->when($request->input('target_grade'), function ($query, $targetGrade) {
                $query->whereIn('target_grade', $targetGrade);
            })
            ->when($request->input('company_status'), function ($query, $companyStatus) {
                $query->whereHas('company', function ($query) use ($companyStatus) {
                    $query->where('status', '=', $companyStatus);
                });
            })
            ->when(!$request->input('sort_by', false), function ($query) {
                $query->orderBy('favorites_count', 'desc');
                $query->orderBy('updated_at', 'desc');
            }) // Default Order
            ->when($request->input('sort_by'), function ($query, $sortBy) use ($request) {
                $query->orderBy(DB::raw("ISNULL($sortBy), $sortBy"), $request->sort_by_order)
                    ->orderByRaw('public_date desc');
            }); // Should be always at the end of the default orders.

        if ($limit > 0) {
            $posts = $posts->limit($limit);
            return $posts->get();
        }

        $posts = $posts->paginate($request->input('paginate', 25));
        return $posts;
    }

    public function dashboardPostRanks($startDate, $endDate)
    {
        return [
            'end_date' => $endDate,
            'start_date' => $startDate,
            'total_application' => InternshipPosts::whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count(), // status 0 is assumed for hiring status
            'rank_1' => $rank1 = $this->getInternshipApplicationCount($startDate, $endDate, ['value' => '0', 'operator' => '=']),
            'rank_2' => $rank2 = $this->getInternshipApplicationCount($startDate, $endDate, ['value' => '1', 'operator' => '>='], ['value' => '2', 'operator' => '=<']),
            'rank_3' => $rank3 = $this->getInternshipApplicationCount($startDate, $endDate, ['value' => '3', 'operator' => '>='], ['value' => '5', 'operator' => '=<']),
            'rank_4' => $rank4 = $this->getInternshipApplicationCount($startDate, $endDate, ['value' => '5', 'operator' => '>']),
            'rank_total' => $totalRank = $rank1 + $rank2 + $rank3 + $rank4,
            'percentage' => $totalRank > 0 ? [
                'rank_1' => round(($rank1 / $totalRank) * 100),
                'rank_2' => round(($rank2 / $totalRank) * 100),
                'rank_3' => round(($rank3 / $totalRank) * 100),
                'rank_4' => round(($rank4 / $totalRank) * 100),
            ] : [],
        ];
    }

    private function getInternshipApplicationCount($startDate, $endDate, array $countRangeStart, array $countRangeEnd = [])
    {
        $result = InternshipPosts::whereHas('applications', function ($query) use ($startDate, $endDate) {
            $query->whereDate('created_at', '>=', $startDate);
            $query->whereDate('created_at', '<=', $endDate);
        }, $countRangeStart['operator'], $countRangeStart['value']);

        /**
         * If end of count range required.
         */
        if (!empty($countRangeEnd) && isset($countRangeEnd['operator']) && isset($countRangeEnd['value'])) {
            $result->whereHas('applications', function ($query) use ($startDate, $endDate) {
                $query->whereDate('created_at', '>=', $startDate);
                $query->whereDate('created_at', '<=', $endDate);
            }, $countRangeStart['operator'], $countRangeStart['value']);
        }

        return $result->count();
    }
}
