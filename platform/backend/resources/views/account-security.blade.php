<!doctype html>
<html lang="fa" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>رمز حساب | ارمغان</title>
<style>body{margin:0;background:#f6f7fb;color:#182333;font-family:Tahoma,sans-serif;line-height:1.9}main{max-width:440px;margin:6vh auto;padding:28px;background:white;border:1px solid #dce3eb;border-radius:20px}h1{font-size:24px;color:#17634c}label{display:block;margin:16px 0 6px}input{box-sizing:border-box;width:100%;padding:13px;border:1px solid #ccd5df;border-radius:10px;font:inherit;direction:ltr}button,a{display:inline-block;margin-top:18px;padding:10px 16px;border-radius:10px;border:0;font:inherit;text-decoration:none}button{background:#0714c2;color:white;cursor:pointer}a{background:#e8f4ef;color:#17634c}.notice{color:#17634c}.error{color:#a51135}small{display:block;color:#596777}@media(max-width:500px){main{margin:20px 12px}}</style></head>
<body><main><h1>رمز حساب</h1><p dir="ltr">{{ $user->email }}</p>
@if(session('status'))<p class="notice" role="status">{{ session('status') }}</p>@endif
@if($errors->any())<p class="error" role="alert">اطلاعات را بررسی کنید. رمز باید حداقل ۱۲ نویسه و شامل حرف و عدد باشد.</p>@foreach($errors->all() as $error)<small class="error">{{ $error }}</small>@endforeach @endif
@if($recentGoogle)<p>مالکیت حساب با گوگل تأیید شد. می‌توانید برای ورود بعدی با ایمیل و رمز، رمز شخصی خود را تعیین کنید.</p>@endif
<form method="POST" action="{{ url('/account/password') }}">@csrf
@if(!$recentGoogle)<label for="current_password">رمز فعلی</label><input id="current_password" name="current_password" type="password" autocomplete="current-password" required>@endif
<label for="password">رمز جدید</label><input id="password" name="password" type="password" minlength="12" maxlength="255" autocomplete="new-password" required>
<small>حداقل ۱۲ نویسه، شامل حرف و عدد.</small>
<label for="password_confirmation">تکرار رمز جدید</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
<button type="submit">ذخیره رمز</button></form>
@if($user->isActiveAdmin())<a href="https://armaghantrading.com/#/admin">ورود به مدیریت</a>@else<a href="https://armaghantrading.com/#/tracking">بازگشت به حساب</a>@endif
</main></body></html>
