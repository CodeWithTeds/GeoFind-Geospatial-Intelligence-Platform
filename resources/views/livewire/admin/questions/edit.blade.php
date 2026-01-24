<div>
    @section('title', 'Edit Question')

    <div class="mb-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600" wire:navigate>
                        <span class="material-icons text-base mr-2">dashboard</span>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <span class="material-icons text-gray-400 text-base mx-1">chevron_right</span>
                        <a href="{{ route('admin.questions.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2" wire:navigate>Questions</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <span class="material-icons text-gray-400 text-base mx-1">chevron_right</span>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <h1 class="text-3xl font-bold text-gray-900">Edit Question</h1>
        <p class="text-gray-600 mt-1">Update details for "{{ $question->title }}"</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form wire:submit="update" class="p-6 md:p-8 space-y-8">
            <!-- Section 1: Basic Info -->
            <div class="border-b border-gray-100 pb-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 text-blue-600 rounded-full h-8 w-8 flex items-center justify-center mr-3">
                        <span class="font-bold text-sm">1</span>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
                </div>
                
                <div class="grid grid-cols-1 gap-6 ml-11">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Question Title</label>
                        <input type="text" wire:model="title" id="title"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors py-3 text-gray-900 placeholder-gray-400 @error('title') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('title') <p class="mt-1 text-sm text-red-600 flex items-center"><span class="material-icons text-sm mr-1">error_outline</span> {{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                        <textarea wire:model="description" id="description" rows="3"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors py-3 text-gray-900 placeholder-gray-400"></textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Location -->
            <div class="border-b border-gray-100 pb-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 text-blue-600 rounded-full h-8 w-8 flex items-center justify-center mr-3">
                        <span class="font-bold text-sm">2</span>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Target Location</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 ml-11">
                    <div>
                        <label for="answer_latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 material-icons text-sm">north</span>
                            </div>
                            <input type="number" step="any" wire:model="answer_latitude" id="answer_latitude"
                                class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors py-3 text-gray-900 placeholder-gray-400 @error('answer_latitude') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                        </div>
                        @error('answer_latitude') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="answer_longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 material-icons text-sm">east</span>
                            </div>
                            <input type="number" step="any" wire:model="answer_longitude" id="answer_longitude"
                                class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors py-3 text-gray-900 placeholder-gray-400 @error('answer_longitude') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                        </div>
                        @error('answer_longitude') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Game Settings -->
            <div>
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 text-blue-600 rounded-full h-8 w-8 flex items-center justify-center mr-3">
                        <span class="font-bold text-sm">3</span>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Game Settings</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 ml-11">
                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-2">Difficulty Level</label>
                        <select wire:model="difficulty" id="difficulty"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors py-3 text-gray-900 @error('difficulty') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                        @error('difficulty') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="tolerance_meters" class="block text-sm font-medium text-gray-700 mb-2">Tolerance (Meters)</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 material-icons text-sm">adjust</span>
                            </div>
                            <input type="number" wire:model="tolerance_meters" id="tolerance_meters"
                                class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors py-3 text-gray-900 placeholder-gray-400 @error('tolerance_meters') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Distance within which the answer is considered correct.</p>
                        @error('tolerance_meters') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end pt-6 border-t border-gray-100 space-x-4">
                <a href="{{ route('admin.questions.index') }}" class="px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200" wire:navigate>
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <span class="material-icons text-sm mr-2" wire:loading.remove>save</span>
                    <span class="material-icons text-sm mr-2 animate-spin" wire:loading>refresh</span>
                    Update Question
                </button>
            </div>
        </form>
    </div>
</div>
