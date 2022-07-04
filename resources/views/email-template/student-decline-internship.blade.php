@component('mail::message')
<p style="margin: 0px"> {{$studentFamilyName}} {{$studentFirstName}} 様</p>
<p style="margin-bottom: 16px; margin-top: 16px;">
いつもコトナルをご利用いただきありがとうございます。
</p>
<p>{{$companyName}}に{{$studentFamilyName}} {{$studentFirstName}} 様の求人応募を辞退するご連絡をしました。</p>
<p style="margin-bottom: 0px">辞退した求人：<a href="{{$internshipPostLink}}" target="_blank">{{$internshipPostTitle}}</a></p>
<p>
※辞退のご連絡が行き違い、企業様からご連絡が来る可能性がございます。<br>
その際は辞退のご意向を直接お伝えください。
</p>
<p style="margin-bottom: 16px">
本メールは、配信専用です。このメールにご返信いただいても、内容の確認およびご返答はできません。<br>
「コトナル」に登録した覚えがないにもかかわらず、本メールを受け取られた方は、お手数ですがメール破棄をお願い致します。
</p>
<p style="margin-bottom: 0px;">{{config('constants.mail_content_end_text')}} </p>
@endcomponent