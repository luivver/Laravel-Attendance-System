<html>

<head>
    <link rel="stylesheet" href="css/bootstrap.css">

    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" src="js/jquery.printPage.js"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Print Izin')</title>
    <meta name="description" content="">

    <!-- Tailwind -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"
        integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>

    <style>
        @import url('https://fonts.googleapis.com/css?family=Karla:400,700&display=swap');

        .font-family-karla {
            font-family: Karla, sans-serif;
        }

        /* ukuran kertas A4 */
        .a4 {
            width: 210mm;
            height: 257mm;
            padding: 10mm;
            box-sizing: border-box;
            background-color: white;
        }

        /* flex to inline-block */
        .inline-block-wrapper {
            display: inline-block;
            vertical-align: top;
            width: 100%;
        }

        .inline-block-item {
            display: inline-block;
            vertical-align: top;
            width: 30%;
            padding: 10px;
            box-sizing: border-box;
        }
    </style>
</head>

<body>
    <div class="a4">
        @yield('content')
    </div>
</body>

</html>
