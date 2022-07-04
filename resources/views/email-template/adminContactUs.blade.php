@component('mail::message')
<p>※自動送信メール</p>
<p>
コトナルにお問い合わせが届きました。<br>
確認をお願いいたします。<br>
</p>
    <p>
        @if(isset($content->company_or_school_name))
        <span>会社名 / 学校名：{{ $content->company_or_school_name }}</span><br>
        @endif
        <span>氏名：{{ $content->name }}</span><br>
        <span>メールアドレス：{{ $content->email }}</span><br>
        @if(isset($content->telephone))
         <span>電話番号：{{ $content->telephone }}</span><br>
         @endif
        <span>お問い合わせ内容：<br>{!! nl2br(e($content->inquiry_content)) !!}</span><br>
    </p>
    <p style="margin-top:16px">このフォームはコトナル公式サイト お問い合わせから送信されました。</p>
@endcomponent
