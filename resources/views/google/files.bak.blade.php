<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Drive Files</title>
</head>
<body>
    <h1>Google Drive Files</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form action="{{ url('google/drive/upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Upload File</button>
    </form>

    <h2>Files:</h2>
    <ul>
        @foreach($files->files as $file)
            <li>{{ $file->name }} (ID: {{ $file->id }})</li>
        @endforeach
    </ul>
</body>
</html>
<!-- resources/views/google/files.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Drive Files</title>
</head>
<body>
    <h1>Google Drive Files</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form action="{{ url('google/drive/upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Upload File</button>
    </form>

    <h2>Files:</h2>
    <ul>
        @foreach($files->files as $file)
            <li>{{ $file->name }} (ID: {{ $file->id }})</li>
        @endforeach
    </ul>
</body>
</html>
