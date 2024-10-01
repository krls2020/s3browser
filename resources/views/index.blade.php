<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S3 Bucket Files</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-5">
<img class="max-h-12 block mx-auto mb-4" src="/images/zerops-logo.svg" />
<h1 class="text-3xl font-bold text-center text-gray-800">Zerops S3 Bucket Browser</h1>
<h2 class="text-xl text-center text-gray-600">Number of files: {{ $fileCount }} | Bucket name: {{$connectionInfo['bucket_name']}}</h2>

<div class="flex justify-end mb-4">
    <form method="GET" action="{{ route('index') }}" class="flex items-center">
        <label for="sortBy" class="font-bold mr-2">Sort by:</label>
        <select name="sortBy" id="sortBy" onchange="this.form.submit()" class="p-2 border border-gray-300 rounded">
            <option value="created_at" {{ request('sortBy') === 'created_at' ? 'selected' : '' }}>Date Created</option>
            <option value="name" {{ request('sortBy') === 'name' ? 'selected' : '' }}>Name</option>
        </select>

        <select name="sortOrder" id="sortOrder" onchange="this.form.submit()" class="p-2 border border-gray-300 rounded ml-2">
            <option value="desc" {{ request('sortOrder') === 'desc' ? 'selected' : '' }}>Descending</option>
            <option value="asc" {{ request('sortOrder') === 'asc' ? 'selected' : '' }}>Ascending</option>
        </select>
    </form>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-8 lg:grid-cols-12 gap-4">
    @foreach ($paginatedFiles as $file)
        <div class="bg-white rounded-lg shadow-md p-4 text-center">
            @php
                $isImage = app('App\Http\Controllers\S3Controller')->isImage($file);
                $fileUrl = Storage::disk('s3')->url($file['path']);
            @endphp

            @if ($isImage)
                {{-- Zobrazit obrázek --}}
                <img src="{{ $fileUrl }}" alt="{{ $file['path'] }}" class="max-w-full h-auto rounded">
            @else
                {{-- Zobrazit ikonu podle typu souboru --}}
                @php
                    $extension = pathinfo($file['path'], PATHINFO_EXTENSION);
                @endphp
                @switch($extension)
                    @case('pdf')
                        <div class="text-6xl text-gray-500">📄</div>
                        @break
                    @case('doc')
                    @case('docx')
                        <div class="text-6xl text-gray-500">📝</div>
                        @break
                    @case('xls')
                    @case('xlsx')
                        <div class="text-6xl text-gray-500">📊</div>
                        @break
                    @case('zip')
                    @case('rar')
                        <div class="text-6xl text-gray-500">🗜️</div>
                        @break
                    @default
                        <div class="text-6xl text-gray-500">📁</div>
                @endswitch
            @endif

            <p class="text-sm text-gray-600 mt-2 break-words">{{ $file['path'] }}</p>

            <div class="mt-2">
                <a href="{{ $fileUrl }}" target="_blank" class="text-blue-500 hover:underline text-sm">Zobrazit</a>
            </div>
        </div>
    @endforeach
</div>

{{-- Stránkování --}}
<div class="mt-5">
    {{ $paginatedFiles->links('vendor.pagination.tailwind') }}
</div>
</body>
</html>
