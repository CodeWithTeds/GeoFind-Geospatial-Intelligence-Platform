<div>
    @section('title', 'Dashboard')

    <div class="mb-8">
        <div class="h-8 w-48 bg-gray-200 rounded animate-pulse"></div>
        <div class="h-4 w-64 bg-gray-200 rounded mt-2 animate-pulse"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach(range(1,3) as $i)
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gray-100 h-12 w-12 animate-pulse"></div>
                <div class="ml-4 space-y-2">
                    <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>
                    <div class="h-6 w-16 bg-gray-200 rounded animate-pulse"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
