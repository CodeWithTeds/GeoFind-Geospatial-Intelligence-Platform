@extends('layouts.admin')

@section('title', 'Edit Question')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.questions.index') }}" class="hover:text-blue-600 transition-colors">Questions</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-800 font-medium">Edit</span>
                </nav>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Edit Question</h1>
                <p class="text-gray-600 mt-1">Update details for "{{ $question->title }}"</p>
            </div>
            <a href="{{ route('admin.questions.index') }}" class="group flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                <span class="material-icons text-lg mr-2 text-gray-400 group-hover:text-gray-600">arrow_back</span>
                Back to List
            </a>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <form action="{{ route('admin.questions.update', $question->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="p-8 space-y-8">
                    <!-- Section: Basic Info -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 mr-3 text-sm font-bold">1</span>
                            Question Details
                        </h3>
                        <div class="grid grid-cols-1 gap-6 ml-11">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Question Title</label>
                                <input type="text" name="title" id="title" value="{{ old('title', $question->title) }}" placeholder="e.g. Find the Hidden Treasure" 
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors py-3 px-4 text-gray-900 placeholder-gray-400 @error('title') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" required>
                                @error('title')
                                    <p class="text-red-600 text-sm mt-1 flex items-center"><span class="material-icons text-sm mr-1">error</span> {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description / Hint</label>
                                <textarea name="description" id="description" rows="4" placeholder="Provide a detailed description or hint for the user..." 
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors py-3 px-4 text-gray-900 placeholder-gray-400 @error('description') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('description', $question->description) }}</textarea>
                                @error('description')
                                    <p class="text-red-600 text-sm mt-1 flex items-center"><span class="material-icons text-sm mr-1">error</span> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    <!-- Section: Location & Difficulty -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-600 mr-3 text-sm font-bold">2</span>
                            Target Location & Settings
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 ml-11">
                            <div>
                                <label for="answer_latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-400 material-icons text-sm">north</span>
                                    </div>
                                    <input type="number" step="any" name="answer_latitude" id="answer_latitude" value="{{ old('answer_latitude', $question->answer_latitude) }}" placeholder="0.000000"
                                        class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors py-3 text-gray-900 placeholder-gray-400 @error('answer_latitude') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" required>
                                </div>
                                @error('answer_latitude')
                                    <p class="text-red-600 text-sm mt-1 flex items-center"><span class="material-icons text-sm mr-1">error</span> {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="answer_longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-400 material-icons text-sm">east</span>
                                    </div>
                                    <input type="number" step="any" name="answer_longitude" id="answer_longitude" value="{{ old('answer_longitude', $question->answer_longitude) }}" placeholder="0.000000"
                                        class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors py-3 text-gray-900 placeholder-gray-400 @error('answer_longitude') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" required>
                                </div>
                                @error('answer_longitude')
                                    <p class="text-red-600 text-sm mt-1 flex items-center"><span class="material-icons text-sm mr-1">error</span> {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-2">Difficulty Level</label>
                                <div class="relative">
                                    <select name="difficulty" id="difficulty" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors py-3 px-4 text-gray-900 appearance-none @error('difficulty') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" required>
                                        <option value="easy" {{ old('difficulty', $question->difficulty) == 'easy' ? 'selected' : '' }}>Easy</option>
                                        <option value="medium" {{ old('difficulty', $question->difficulty) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="hard" {{ old('difficulty', $question->difficulty) == 'hard' ? 'selected' : '' }}>Hard</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <span class="material-icons">expand_more</span>
                                    </div>
                                </div>
                                @error('difficulty')
                                    <p class="text-red-600 text-sm mt-1 flex items-center"><span class="material-icons text-sm mr-1">error</span> {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tolerance_meters" class="block text-sm font-medium text-gray-700 mb-2">Tolerance Radius (meters)</label>
                                <div class="relative rounded-md shadow-sm">
                                    <input type="number" name="tolerance_meters" id="tolerance_meters" value="{{ old('tolerance_meters', $question->tolerance_meters) }}" placeholder="50"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors py-3 px-4 text-gray-900 placeholder-gray-400 @error('tolerance_meters') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" required min="0">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-400 text-sm">meters</span>
                                    </div>
                                </div>
                                @error('tolerance_meters')
                                    <p class="text-red-600 text-sm mt-1 flex items-center"><span class="material-icons text-sm mr-1">error</span> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-6 border-t border-gray-200 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.questions.index') }}" class="text-gray-700 hover:text-gray-900 font-medium text-sm px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-sm hover:shadow transition-all duration-200 flex items-center">
                        <span class="material-icons text-sm mr-2">save</span>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
