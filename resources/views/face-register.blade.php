<!DOCTYPE html>
<html>
<head>
    <title>تسجيل بصمة الوجه</title>
</head>
<body>

<h2>تسجيل بصمة الوجه</h2>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<form method="POST" action="/face-register">
    @csrf
    <button type="submit">
        تسجيل بصمة الوجه
    </button>
</form>

</body>
</html>
