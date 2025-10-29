<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    @include('admin.layouts.head-tag')
</head>

<body class="gradient-bg min-h-screen">
    
    @include('admin.layouts.partials.sidebar')

    <!-- Main Content -->
    <div class="lg:mr-64 min-h-screen">
        
        @include('admin.layouts.partials.header')

        @yield('content')


    </div>

    @include('admin.layouts.scripts')
</body>

</html>