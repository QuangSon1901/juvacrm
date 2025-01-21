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
    <table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Size</th>
            <th>Preview / Download</th>
        </tr>
    </thead>
    <tbody>
        @foreach($files->files as $file)
            <tr>
                <td>{{ $file->name }}</td>
                <td>{{ $file->mimeType }}</td>
                <td>
                    @if(isset($file->size))
                        {{ formatBytes($file->size) }}
                    @else
                        Unknown
                    @endif
                </td>
                <td>
                    @if(Str::startsWith($file->mimeType, 'image/'))
                        <img src="https://drive.google.com/thumbnail?id={{ $file->id }}&sz=w150">
                    @else
                        <!-- Hiển thị link tải xuống -->
                        <a href="https://drive.google.com/uc?id={{ $file->id }}&export=download" download>Download</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
