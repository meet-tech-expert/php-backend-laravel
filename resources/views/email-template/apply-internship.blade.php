
@component('mail::message')
	<p>{{$companyName}} 様</p>
			<p>（学生様は企業様からのご連絡をお待ちください。本メールは企業様にBCCでお送りしているものです。）</p>
			<p>
			いつもコトナルをご利用いただきありがとうございます。<br>
			御社の求人広告に、{{$studentFamilyName}} {{$studentFirstName}} 様から応募がありました。<br>
			以下の情報を確認の上、採用プロセスにお進みください。<br>
			</p>
			<a href="{{$internshipPostLink}}" target="_blank">{{$internshipPostTitle}}</a>
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
			<p>
			恐れ入りますが、御社が採用プロセスに進む際は、メールのCCにコトナル事務局（intern@kotonaru.co.jp ）を入れていただきますようお願いします。<br>
			本メールの「全員返信機能」をつかってやりとりいただくことも可能です。<br>
			「コトナル」を利用した覚えがないにもかかわらず、本メールを受け取られた方は、お手数ですがメール破棄をお願い致します。<br>
			</p>
			<br>
			<br>
<p style="margin-bottom: 0px;">{{config('constants.mail_content_end_text')}} </p>
@endcomponent