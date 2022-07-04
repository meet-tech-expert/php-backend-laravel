<?php

return [

    'internship_images_path' => 'internshippost/',
    'internship_images_content_path' => 'internshippost/content/',
    'media_images_path' => 'mediapost/',
    'media_images_content_path' => 'mediapost/content/',
    'company_images_path' => 'company',
    'mail_content_end_text' => 'コトナル事務局',
    'kt_admin_email' => env('ADMIN_MAIL_ADDRESS', 'info@kotonaru.co.jp'),

    'period' => [
        [
            'id' => 1,
            'name' => '1ヶ月',
        ],
        [
            'id' => 2,
            'name' => '1〜3ヶ月',
        ],
        [
            'id' => 3,
            'name' => '3ヶ月〜',
        ],
    ],

    'workload' => [
        [
            'id' => 1,
            'name' => '10h未満/週',
        ],
        [
            'id' => 2,
            'name' => '10〜20h/週',
        ],
        [
            'id' => 3,
            'name' => '20h以上/週',
        ],
    ],

    'wage' => [
        [
            'id' => 1,
            'name' => '¥1,000〜',
        ],
        [
            'id' => 2,
            'name' => '¥1,500〜',
        ],
        [
            'id' => 3,
            'name' => '¥2,000〜',
        ],
    ],

    'target_grade' => [
        [
            'id' => 1,
            'name' => '1〜2年生',
        ],
        [
            'id' => 2,
            'name' => '1〜3年生',
        ],
        [
            'id' => 3,
            'name' => '学年問わず',
        ],
    ],

    'educational_facility_type' => [
        [
            'id' => 1,
            'name' => '大学',
        ],
        [
            'id' => 2,
            'name' => '大学院',
        ],
        [
            'id' => 3,
            'name' => '短期大学',
        ],
        [
            'id' => 4,
            'name' => '専門学校',
        ],
        [
            'id' => 5,
            'name' => '高校/高専',
        ],
        [
            'id' => 6,
            'name' => 'その他',
        ],
    ],

    'reviews_option' => [
        [
            'id' => 1,
            'name' => 'リーダーシップ',
        ],
        [
            'id' => 2,
            'name' => '大胆さ',
        ],
        [
            'id' => 3,
            'name' => '外向的',
        ],
        [
            'id' => 4,
            'name' => '創造性',
        ],
        [
            'id' => 5,
            'name' => '協調性',
        ],
        [
            'id' => 6,
            'name' => '綿密さ',
        ],
        [
            'id' => 7,
            'name' => '内省的',
        ],
        [
            'id' => 8,
            'name' => '論理性',
        ],
    ],

    'cancel_reasons' => [
        [
            'id' => 1,
            'name' => '学業の兼ね合いで就業時間が合わなくなった',
        ],
        [
            'id' => 2,
            'name' => '他に興味のあるインターンを見つけた',
        ],
        [
            'id' => 3,
            'name' => '他のインターンに採用された',
        ],
        [
            'id' => 4,
            'name' => 'その他',
        ],
    ],

    'withdrawl_reasons' => [
        [
            'id' => 1,
            'name' => '就職先が決まった'
        ],
        [
            'id' => 2,
            'name' => '自分に合うインターン情報が無かった'
        ],
        [
            'id' => 3,
            'name' => '忙しくなり、インターンに参加できなくなった'
        ],
        [
            'id' => 4,
            'name' => 'オンラインインターンシップに参加する必要が無くなった'
        ],
        [
            'id' => 5,
            'name' => 'オンラインインターンシップへの興味が無くなった'
        ],
        [
            'id' => 6,
            'name' => 'その他'
        ],
        
    ],

    'application_status' => [
        [
            'id' => 1,
            'name' => '応募済',
            'en' => 'applied',
        ],
        [
            'id' => 2,
            'name' => '合格済',
            'en' => 'qualified',
        ],
        [
            'id' => 3,
            'name' => '完了',
            'en' => 'done',
        ],
        [
            'id' => 4,
            'name' => '不合格',
            'en' => 'not-qualified',
        ],
        [
            'id' => 5,
            'name' => '辞退済',
            'en' => 'declined',
        ],
    ],
];
