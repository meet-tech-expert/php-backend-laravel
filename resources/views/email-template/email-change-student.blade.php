@component('mail::message')
{{ $data['student_family_name'] }} {{ $data['student_first_name'] }}様<br><br>
<p style="margin-top: 16px">いつもコトナルを利用いただき、ありがとうございます。</p>
<p style="margin-bottom: 0px">
メールアドレスの変更は、まだ完了しておりません。<br>
完了するには、こちらからコトナルにアクセスしてください。<br>
アクセス有効期限は24時間です。<br>
それ以降は、再度マイページからメールアドレスの変更をお願いいたします。<br>
<p>
@component('mail::button', ['url' => $data['url']])
アクセスする
@endcomponent
<p style="margin-bottom: 0px">
ボタンがうまく動作しない場合は下記のURLをご利用ください。
</p>
<br>
<a href="{{ $data['url'] }}" style="width: 100%;">{{ $data['url'] }}</a>
<p style="margin-top:16px">
本メールは、配信専用です。このメールにご返信いただいても、内容の確認およびご返答はできません。<br>
「コトナル」に登録した覚えがないにもかかわらず、本メールを受け取られた方は、お手数ですがメール破棄をお願い致します。<br>
ま破棄をお願い致します。
</p>
<br> <br>
<p style="margin-bottom: 0px;"> {{config('constants.mail_content_end_text')}} </p>
@endcomponent