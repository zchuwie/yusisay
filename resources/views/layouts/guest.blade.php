<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Yusisay</title>
 
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=1">
 
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .logo-text {
            font-family: 'Pacifico', cursive;
            font-size: 64px;
            color: white;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .gradient-side {
            background: linear-gradient(135deg, #FF9013 0%, #FF6B6B 100%);
            position: relative;
            overflow: hidden;
        }

        .gradient-side::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .decorative-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .circle-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            right: -100px;
        }

        .circle-2 {
            width: 200px;
            height: 200px;
            bottom: -50px;
            left: -50px;
        }

        .circle-3 {
            width: 150px;
            height: 150px;
            top: 50%;
            right: 10%;
        }

        @media (max-width: 768px) {
            .gradient-side {
                display: none;
            }
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex"> 
        <div class="gradient-side hidden md:flex md:w-1/2 flex-col items-center justify-center p-12 relative">
            <div class="decorative-circle circle-1"></div>
            <div class="decorative-circle circle-2"></div>
            <div class="decorative-circle circle-3"></div>

            <div class="relative z-10 text-center">
                <h1 class="logo-text mb-6">Yusisay</h1> 
                <p class="text-white/80 text-lg">Connect, express, and be heard.</p> 
            </div>
        </div>
 
        <div class="w-full md:w-1/2 flex items-center justify-center p-6 bg-gray-50">
            <div class="w-full max-w-md"> 
                <div class="md:hidden text-center mb-8">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-[#FF9013] to-[#FF6B6B] bg-clip-text text-transparent"
                        style="font-family: 'Pacifico', cursive;">
                        Yusisay
                    </h1>
                    <p class="text-gray-600 text-sm mt-2">Share your thoughts anonymously</p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>

</html>
