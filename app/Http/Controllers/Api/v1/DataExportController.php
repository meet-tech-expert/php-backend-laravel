<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Applications;
use App\Models\Company;
use App\Models\EducationFacilities;
use App\Models\Students;
use League\Csv\Writer;
use SplTempFileObject;

class DataExportController extends Controller
{
    // Export Company as csv
    public function exportCompany()
    {
        // get all companies with business industry name
        $companies = Company::with([
            'businessIndustry' => function ($query) {
                $query->withTrashed();
            },
        ])->get();
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne([
            'ID',
            '内部ID',
            '企業名',
            '企業名カナ',
            '業界',
            '住所',
            '電話',
            'メール1',
            'メール2',
            'メール3',
            'ホームページ',
            '担当者名',
            'メモ',
            'ステータス',
            '作成日',
            '更新日',
            '削除日',
        ]);

        foreach ($companies as $row) {
            $csv->insertOne([
                isset($row->id) ? $row->id : '',
                isset($row->internal_company_id) ? $row->internal_company_id : '',
                isset($row->name) ? $row->name : '',
                isset($row->furigana_name) ? $row->furigana_name : '',
                isset($row->businessIndustry) ? $row->businessIndustry['name'] : '',
                isset($row->office_address) ? $row->office_address : '',
                isset($row->office_phone) ? $row->office_phone : '',
                isset($row->office_email1) ? $row->office_email1 : '',
                isset($row->office_email2) ? $row->office_email2 : '',
                isset($row->office_email3) ? $row->office_email3 : '',
                isset($row->website_url) ? $row->website_url : '',
                isset($row->client_liason) ? $row->client_liason : '',
                isset($row->admin_memo) ? $row->admin_memo : '',
                $row->status == 1 ? 'アクティブ' : '非アクティブ',
                isset($row->created_at) ? $row->created_at : '',
                isset($row->updated_at) ? $row->updated_at : '',
                isset($row->soft_deleted_at) ? $row->soft_deleted_at : '',
            ]);
        }

        return $this->sendResponse(['csv' => $csv->toString()]);
    }

    public function exportStudent()
    {
        $students = Students::with([
            'educationFacility' => function ($query) {
                $query->withTrashed();
            },
        ])
            ->whereNotNull('email_valid')
            ->whereNotNull('student_internal_id')
            ->whereIn('status', [1, 2, 3])
            ->get();
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne([
            'ID',
            '内部ID',
            '姓',
            '名',
            'セイ',
            'メイ',
            'メールアドレス',
            '学校名',
            '卒業予定',
            '一言アピール',
            'ステータス',
            '登録日',
            '削除日',
        ]);

        foreach ($students as $row) {
            $csv->insertOne([
                isset($row->id) ? $row->id : '',
                isset($row->student_internal_id) ? $row->student_internal_id : '',
                isset($row->family_name) ? $row->family_name : '',
                isset($row->first_name) ? $row->first_name : '',
                isset($row->family_name_furigana) ? $row->family_name_furigana : '',
                isset($row->first_name_furigana) ? $row->first_name_furigana : '',
                isset($row->email_valid) ? $row->email_valid : '',
                isset($row->educationFacility['name']) ? $row->educationFacility['name'] : '',
                isset($row->graduate_year) ? $row->graduate_year . '/' . $row->graduate_month : '',
                isset($row->self_introduction) ? $row->self_introduction : '',
                $row->status == 1 ? "アクティブ" : ($row->status == 2 ? "非アクティブ" : " 退会済"),
                isset($row->created_at) ? $row->created_at : '',
                isset($row->deleted_at) ? $row->deleted_at : '',
            ]);
        }

        return $this->sendResponse(['csv' => $csv->toString()]);
    }

    public function exportApplication()
    {
        $applications = Applications::with([
            'student' => function ($query) {
                $query->withTrashed();
            },
            'company' => function ($query) {
                $query->withTrashed();
            },
            'internshipPost' => function ($query) {
                $query->withTrashed();
            },
        ])->get();

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne([
            'ID',
            '求人ID',
            '企業名',
            '求人タイトル',
            'ステータス',
            '学生ID',
            '学生名',
            '大学名',
            '学生メールアドレス',
            '応募日',
            '更新日',
        ]);

        foreach ($applications as $row) {
            $csv->insertOne([
                isset($row->id) ? $row->id : '',
                isset($row->internship_post_id) ? $row->internship_post_id : '',
                isset($row->company_id) && isset($row->company['name']) ? $row->company['name'] : '',
                isset($row->internshipPost) && isset($row->internshipPost['title']) ? $row->internshipPost['title'] : '',
                isset($row->status) ? $this->getApplicationStatus($row->status) : '',
                isset($row->student_id) && isset($row->student['student_internal_id']) ? $row->student['student_internal_id'] : '',
                isset($row->student) ? $row->student['family_name'] . $row->student['first_name'] : '',
                isset($row->student['education_facility_id']) ? EducationFacilities::find($row->student['education_facility_id'])->name : '',
                isset($row->student_id) && isset($row->student['email_valid']) ? $row->student['email_valid'] : '',
                isset($row->created_at) ? $row->created_at : '',
                isset($row->updated_at) ? $row->updated_at : '',
            ]);
        }

        return $this->sendResponse(['csv' => $csv->toString()]);
    }

    private function getApplicationStatus($status)
    {
        switch ($status) {
            case 1:
                return '応募済';
                break;
            case 2:
                return '合格済';
                break;
            case 3:
                return '完了';
                break;
            case 4:
                return '不合格';
                break;
            case 5:
                return '辞退済';
                break;

            default:
                return '';
                break;
        }
    }

}
