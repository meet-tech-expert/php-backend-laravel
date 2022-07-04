@component('mail::message')
<h3>{{$adminUserName}} 様 </h3><br>
<p>
いつもKotonaruを利用いただき、ありがとうございます。<br>
パスワードの再発行についてお知らせいたします。<br>
下記ボタンをクリックし、新しいパスワードの設定をお願いいたします。
</p>
@component('mail::button', ['url' => $passwordResetUrl])
ここをクリック
@endcomponent
<br><br>
<p style="margin-bottom: 0px;"> ボタンがうまく動作しない場合は下記のURLをコピーしてご利用ください。</p>
<br>
<a href="{{$passwordResetUrl}}">{{$passwordResetUrl}}</a>
<br><br>

本メールは、配信専用のアドレスで配信されています。このメールにご返信 いただいても、内容の確認およびご返答はできません。ご了承ください。
当サイトへの登録をした覚えがないのに、このメールを受け取られた方は、お手数ではございますがこのまま破棄をお願い致します。
<br><br>
{{config('constants.mail_content_end_text')}}
@endcomponent