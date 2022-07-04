
@component('mail::message')
<p> この度は、「コトナル」への会員登録依頼を頂きまして、ありがとうございます。</p>
<p style="margin-bottom: 0px;">
こちらから会員登録のお手続きを24時間以内に、お願いいたします。
</p>
@component('mail::button', ['url' => $data['url']])
会員登録にすすむ
@endcomponent
<p style="margin-bottom: 0px;"> ボタンがうまく動作しない場合は以下のURLをご利用ください。</p>
<br>
<a href="{{ $data['url'] }}">{{ $data['url'] }}</a> 
<p style="margin-top:16px">
本メールは、配信専用のアドレスで配信されています。このメールにご返信 いただいても、内容の確認およびご返答はできません。ご了承ください。<br>
当サイトについて覚えがないのに、このメールを受け取られた方は、お手数ではございますがこのまま破棄をお願い致します。
</p>
<br> <br>
<p style="margin-bottom: 0px;"> {{config('constants.mail_content_end_text')}} </p>
@endcomponent