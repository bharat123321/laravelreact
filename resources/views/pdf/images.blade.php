<!-- resources/views/pdf/images.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Images PDF</title>
</head>
<body>
    @foreach ($imagePaths as $path)
        <div style="page-break-after: always;">
            <img src="{{ $path }}" style="width: 100%; height: auto;" />
        </div>
    @endforeach
</body>
</html>
