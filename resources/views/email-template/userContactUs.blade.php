@component('mail::message')
<p>
{{ $content->name }} 様
</p>
<p>
コトナルへのお問い合わせを頂きまして、ありがとうございます。<br>
以下の内容で送信いたしました。
</p>
<p>
@if(isset($content->company_or_school_name))
会社名 / 学校名：{{ $content->company_or_school_name }}<br>
@endif
氏名：{{ $content->name }}<br>
メールアドレス：{{ $content->email }}<br>
@if(isset($content->telephone))
電話番号：{{ $content->telephone }}<br>
@endif
お問い合わせ内容：<br>{!! nl2br(e($content->inquiry_content)) !!}
</p>
<p style="margin-top: 16px;">
2営業日以内に、担当者よりご連絡いたします。<br>
よろしくお願いいたします。
</p>
<p>
本メールは、配信専用です。このメールにご返信いただいても、内容の確認およびご返答はできません。<br>
「コトナル」に登録した覚えがないにもかかわらず、本メールを受け取られた方は、お手数ですがメール破棄をお願い致します。
</p>
<br>
<p style="margin-bottom: 0px;">{{config('constants.mail_content_end_text')}}</p>
@endcomponent
