<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Authors</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $record->authors ?: 'Unknown' }}</p>
        </div>

        <div>
            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Publication</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $record->publication ?: 'N/A' }}</p>
        </div>

        <div>
            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Date</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $record->publication_date?->format('M Y') ?: 'Unknown' }}</p>
        </div>

        @if($record->doi)
        <div>
            <h4 class="font-semibold text-gray-900 dark:text-gray-100">DOI</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $record->doi }}</p>
        </div>
        @endif

        @if($record->quality_score)
        <div>
            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Quality Score</h4>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                {{ $record->quality_score >= 8 ? 'bg-green-100 text-green-800' :
                   ($record->quality_score >= 6 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                {{ $record->quality_score }}/10
            </span>
        </div>
        @endif

        @if($record->tags)
        <div class="md:col-span-2">
            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Tags</h4>
            <div class="flex flex-wrap gap-1">
                @foreach($record->tags as $tag)
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $tag }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    @if($record->abstract)
    <div>
        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Abstract</h4>
        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $record->abstract }}</p>
    </div>
    @endif

    @if($record->key_findings)
    <div>
        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Key Findings</h4>
        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $record->key_findings }}</p>
    </div>
    @endif

    @if($record->url)
    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ $record->url }}" target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
            View Full Paper
        </a>
    </div>
    @endif
</div>