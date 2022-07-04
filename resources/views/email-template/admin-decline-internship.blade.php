@component('mail::message')
<p>※自動送信メール</p>
<p>{{$studentFamilyName}} {{$studentFirstName}} 様が、以下の求人応募を辞退しました。
確認をお願いいたします。
</p>
<p>
求人ID：{{$internalInternshipId}}<br>
求人タイトル：{{$internshipPostTitle}}<br>
</p>
<p style="margin-top: 16px">
このフォームはコトナル公式サイトから送信されました。
</p>
@endcomponent
