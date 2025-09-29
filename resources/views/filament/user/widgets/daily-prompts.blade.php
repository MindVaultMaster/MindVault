<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Daily Reflection
        </x-slot>

        <div class="space-y-4">
            @php
                $data = $this->getViewData();
                $hasEntryToday = $data['hasEntryToday'];
                $todaysPrompt = $data['todaysPrompt'];
                $avgFocus = $data['avgFocus'];
                $avgMood = $data['avgMood'];
                $avgSleep = $data['avgSleep'];
                $recentEntries = $data['recentEntries'];
            @endphp

            @if(!$hasEntryToday)
                <div class="p-4 bg-blue-50 dark:bg-blue-900/50 rounded-lg border border-blue-200 dark:border-blue-700">
                    <div class="flex items-center">
                        <x-heroicon-o-light-bulb class="h-5 w-5 text-blue-500 mr-2" />
                        <span class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            Haven't logged today yet?
                        </span>
                    </div>
                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                        Create your journal entry to track today's experience.
                    </p>
                    <div class="mt-3">
                        <a href="{{ route('filament.user.resources.journal-entries.create') }}"
                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <x-heroicon-o-plus class="h-4 w-4 mr-1" />
                            Log Today's Entry
                        </a>
                    </div>
                </div>
            @else
                <div class="p-4 bg-green-50 dark:bg-green-900/50 rounded-lg border border-green-200 dark:border-green-700">
                    <div class="flex items-center">
                        <x-heroicon-o-check-circle class="h-5 w-5 text-green-500 mr-2" />
                        <span class="text-sm font-medium text-green-800 dark:text-green-200">
                            Great job! You've logged today's entry.
                        </span>
                    </div>
                </div>
            @endif

            <!-- Today's Reflection Prompt -->
            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                    💭 Today's Reflection
                </h4>
                <p class="text-sm text-gray-700 dark:text-gray-300 italic">
                    "{{ $todaysPrompt }}"
                </p>
            </div>

            <!-- Quick Insights -->
            @if($recentEntries->count() > 0)
                <div class="grid grid-cols-3 gap-3">
                    @if($avgFocus)
                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="text-center">
                                <div class="text-lg font-semibold {{ $avgFocus >= 7 ? 'text-green-600' : ($avgFocus >= 5 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($avgFocus, 1) }}/10
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">7-day avg focus</div>
                            </div>
                        </div>
                    @endif

                    @if($avgMood)
                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="text-center">
                                <div class="text-lg font-semibold {{ $avgMood >= 7 ? 'text-green-600' : ($avgMood >= 5 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($avgMood, 1) }}/10
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">7-day avg mood</div>
                            </div>
                        </div>
                    @endif

                    @if($avgSleep)
                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="text-center">
                                <div class="text-lg font-semibold {{ $avgSleep >= 7 ? 'text-green-600' : ($avgSleep >= 5 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($avgSleep, 1) }}/10
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">7-day avg sleep</div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Quick Tips -->
            <div class="p-4 bg-purple-50 dark:bg-purple-900/50 rounded-lg border border-purple-200 dark:border-purple-700">
                <h4 class="text-sm font-medium text-purple-900 dark:text-purple-100 mb-2">
                    💡 Tracking Tips
                </h4>
                <ul class="text-sm text-purple-800 dark:text-purple-200 space-y-1">
                    <li>• Log entries consistently for better insights</li>
                    <li>• Note timing - when you take substances matters</li>
                    <li>• Track sleep quality to understand recovery</li>
                    <li>• Record any side effects, even minor ones</li>
                </ul>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
