@component('mail::message')
<p>{{$companyName}} 様</p>
			<br>
			<p>
			いつもコトナルをご利用いただきありがとうございます。<br>
			{{$studentFamilyName}} {{$studentFirstName}} 様が、御社の求人応募を辞退しました。<br>
			以下の情報を確認の上、ご対応をお願いいたします。
			</p>
			<p style="margin-bottom: 0px">
			<a href="{{$internshipPostLink}}" target="_blank">{{$internshipPostTitle}}</a>
			</p>
			<br>
			<p>
			応募者名：{{$studentFamilyName}} {{$studentFirstName}} ( {{$studentFamilyNameFurigana}} {{$studentFirstNameFurigana}} ) <br>
			メールアドレス：{{$email}} <br>
			学校名：{{$university}} <br>
			卒業予定：{{$year}}年 {{$month}}月 <br>
			一言アピール：<br>
			@if($selfIntroduction)
			<span style="margin-bottom: 0px; width: 554px; height: auto">
			{{$selfIntroduction}}
			</span>
			@endif
			</p>
			<br>
			<p>
            本メールは、配信専用です。このメールにご返信いただいても、内容の確認およびご返答はできません。<br>
			「コトナル」に登録した覚えがないにもかかわらず、本メールを受け取られた方は、お手数ですがメール破棄をお願い致します。<br>
			</p>
			<br>
<p style="margin-bottom: 0px;"> {{config('constants.mail_content_end_text')}} </p>
@endcomponent