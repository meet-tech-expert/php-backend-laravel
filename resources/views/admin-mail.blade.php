@component('mail::message')
<h3>{{$adminUserName}} 様 </h3>
<p>
パスワードの再発行についてお知らせいたします。<br>
こちらから、新しいパスワードの設定をお願いいたします。
</p>

@component('mail::button', ['url' => $passwordResetUrl])
パスワード再発行に進む
@endcomponent

<p style="margin-top: 16px;">
本メールは、配信専用です。このメールにご返信いただいても、内容の確認およびご返答はできません。<br>
「コトナル」に登録した覚えがないにもかかわらず、本メールを受け取られた方は、お手数ですがメール破棄をお願い致します。<br>
</p>
{{config('constants.mail_content_end_text')}}
@endcomponent