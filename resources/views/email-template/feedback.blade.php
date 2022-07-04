@component('mail::message')
<p>
{{$studentName}} 様
 </p> <br>

<p>いつもコトナルを利用いただき、ありがとうございます。<br>
{{$company_name}}からフィードバックが届きました。
</p>

<p style="margin-bottom: 0px;"> マイページから確認してみましょう。</p>
@component('mail::button', ['url' =>  $url])
フィードバックを確認する
@endcomponent
<p style="margin-bottom: 0px;">ボタンがうまく動作しない場合は以下のURLをご利用ください。</p>
<a href="{{$url}}" target="_blank">{{$url}}</a> <br><br>

<p style="margin-top: 16px">外から見た自分の「評価された力」や「期待したい力を」を知ることで、自己分析に役立てたり新<br>しい自分を発見しましょう！</p>

<p>
本メールは、配信専用です。このメールにご返信いただいても、内容の確認およびご返答はできません。<br>
「コトナル」に登録した覚えがないにもかかわらず、本メールを受け取られた方は、お手数ですがメール破棄をお願い致します。<br>
</p><br>

<p style="margin-bottom: 0px;"> {{config('constants.mail_content_end_text')}}</p>
@endcomponent