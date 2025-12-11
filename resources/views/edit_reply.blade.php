<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reply - VincenThinks</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-2xl border border-gray-200">
        <h2 class="text-2xl font-light text-gray-800 mb-6">Edit Reply</h2>
        <form action="{{ route('reply.update', $reply->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <textarea name="content" rows="4" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:border-red-800">{{ $reply->content }}</textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <a href="{{ url()->previous() }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</a>
                <button type="submit" class="bg-red-800 text-white px-6 py-2 rounded hover:bg-red-900">Update Reply</button>
            </div>
        </form>
    </div>
</body>
</html>