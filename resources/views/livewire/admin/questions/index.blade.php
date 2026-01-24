<div>
    @section('title', 'Manage Questions')

    <div class="mb-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600" wire:navigate>
                        <span class="material-icons text-base mr-2">dashboard</span>
                        Dashboard
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <span class="material-icons text-gray-400 text-base mx-1">chevron_right</span>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Questions</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Questions Manager</h1>
                <p class="text-gray-600 mt-1">Manage your game questions, coordinates, and difficulty levels.</p>
            </div>
            <a href="{{ route('admin.questions.create') }}" class="mt-4 md:mt-0 inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-all duration-200" wire:navigate>
                <span class="material-icons text-base mr-2">add_circle_outline</span>
                Add New Question
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm flex items-center" role="alert">
            <span class="material-icons text-green-500 mr-3">check_circle</span>
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Question Details
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Location
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Difficulty
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Tolerance
                        </th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($questions as $question)
                        <tr class="hover:bg-gray-50 transition-colors duration-150" wire:key="{{ $question->id }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                                        <span class="material-icons text-xl">help_outline</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $question->title }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">{{ Str::limit($question->description, 60) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-xs text-gray-500 flex items-center">
                                        <span class="material-icons text-[14px] mr-1 text-gray-400">north</span> {{ number_format($question->answer_latitude, 6) }}
                                    </span>
                                    <span class="text-xs text-gray-500 flex items-center mt-1">
                                        <span class="material-icons text-[14px] mr-1 text-gray-400">east</span> {{ number_format($question->answer_longitude, 6) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($question->difficulty === 'easy') bg-green-100 text-green-800 border border-green-200
                                    @elseif($question->difficulty === 'medium') bg-yellow-100 text-yellow-800 border border-yellow-200
                                    @else bg-red-100 text-red-800 border border-red-200 @endif">
                                    {{ ucfirst($question->difficulty) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <div class="flex items-center">
                                    <span class="material-icons text-base text-gray-400 mr-1">adjust</span>
                                    {{ $question->tolerance_meters }} meters
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('admin.questions.edit', $question->id) }}" class="text-gray-400 hover:text-blue-600 transition-colors duration-200" title="Edit" wire:navigate>
                                        <span class="material-icons">edit</span>
                                    </a>
                                    <button wire:click="delete({{ $question->id }})" wire:confirm="Are you sure you want to delete this question? This action cannot be undone." class="text-gray-400 hover:text-red-600 transition-colors duration-200 focus:outline-none" title="Delete">
                                        <span class="material-icons">delete_outline</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <span class="material-icons text-3xl text-gray-400">search_off</span>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">No questions found</h3>
                                    <p class="text-gray-500 mt-1 mb-6 max-w-sm">Get started by creating your first question for the game.</p>
                                    <a href="{{ route('admin.questions.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" wire:navigate>
                                        <span class="material-icons text-sm mr-2">add</span>
                                        Create Question
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($questions->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $questions->links() }}
            </div>
        @endif
    </div>
</div>
