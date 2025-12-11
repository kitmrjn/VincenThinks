<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Question - VincenThinks</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-2xl border border-gray-200">
        <h2 class="text-2xl font-light text-gray-800 mb-6">Edit Question</h2>
        <form action="{{ route('question.update', $question->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                <input type="text" name="title" value="{{ $question->title }}" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:border-red-800">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Content</label>
                <textarea name="content" rows="6" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:border-red-800">{{ $question->content }}</textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <a href="{{ route('question.show', $question->id) }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</a>
                <button type="submit" class="bg-red-800 text-white px-6 py-2 rounded hover:bg-red-900">Update Question</button>
            </div>
        </form>
    </div>
</body>
</html>