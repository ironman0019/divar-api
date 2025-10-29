<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فرم ورود و ثبت نام</title>
    <link rel="stylesheet" href="{{ asset('admin-assets/css/auth-style.css') }}">
</head>

<body>
    <div class="container">
        <div class="curved-shape"></div>
        <div class="curved-shape2"></div>
        <div class="form-box Login">
            <h2 class="animation" style="--D:0; --S:21">ورود</h2>
            <form action="{{ route('login.auth') }}" method="POST">
                @csrf
                <div class="input-box animation" style="--D:1; --S:22">
                    <input type="text" name="mobile" required>
                    <label for="">شماره موبایل</label>
                    <box-icon type='solid' name='user' color="gray"></box-icon>
                </div>
                @error('mobile')
                    <span class="text-danger font-size-12">{{ $message }}</span>
                @enderror

                <div class="input-box animation" style="--D:2; --S:23">
                    <input type="password" name="password" required>
                    <label for="">رمز عبور</label>
                    <box-icon name='lock-alt' type='solid' color="gray"></box-icon>
                </div>
                @error('password')
                    <span class="text-danger font-size-12">{{ $message }}</span>
                @enderror

                <div class="input-box animation" style="--D:3; --S:24">
                    <button class="btn" type="submit">ورود</button>
                </div>

                {{-- <div class="regi-link animation" style="--D:4; --S:25">
                    <p>حساب کاربری ندارید؟ <br> <a href="#" class="SignUpLink">ثبت نام</a></p>
                </div> --}}
            </form>
        </div>

        <div class="info-content Login">
            <h2 class="animation" style="--D:0; --S:20">خوش آمدید!</h2>
            <p class="animation" style="--D:1; --S:21">ورود به پنل ادمین</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="form-box Register">
            <h2 class="animation" style="--li:17; --S:0">ثبت نام</h2>
            <form action="#">
                <div class="input-box animation" style="--li:18; --S:1">
                    <input type="text" required>
                    <label for="">شماره موبایل</label>
                    <box-icon type='solid' name='user' color="gray"></box-icon>
                </div>

                <div class="input-box animation" style="--li:19; --S:2">
                    <input type="email" required>
                    <label for="">ایمیل</label>
                    <box-icon name='envelope' type='solid' color="gray"></box-icon>
                </div>

                <div class="input-box animation" style="--li:19; --S:3">
                    <input type="password" required>
                    <label for="">رمز عبور</label>
                    <box-icon name='lock-alt' type='solid' color="gray"></box-icon>
                </div>

                <div class="input-box animation" style="--li:20; --S:4">
                    <button class="btn" type="submit">ثبت نام</button>
                </div>

                <div class="regi-link animation" style="--li:21; --S:5">
                    <p>حساب کاربری دارید؟ <br> <a href="#" class="SignInLink">ورود</a></p>
                </div>
            </form>
        </div>

        <div class="info-content Register">
            <h2 class="animation" style="--li:17; --S:0">خوش آمدید!</h2>
            <p class="animation" style="--li:18; --S:1">از حضور شما در کنار ما خوشحالیم. اگر به کمک نیاز دارید، در خدمت
                شما هستیم.</p>
        </div>

    </div>

    <script src="{{ asset('admin-assets/js/auth-script.js') }}"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

</body>

</html>
