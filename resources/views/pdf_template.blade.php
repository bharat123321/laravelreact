<!DOCTYPE html>
<html>
<head>
    <title>PDF Document</title>
</head>
<body>
    @foreach($imageUrls as $imageUrl)
        <div>
            <img src="{{ $imageUrl }}" alt="Image" style="width: 100%;"/>
        </div>
    @endforeach
</body>
</html>
