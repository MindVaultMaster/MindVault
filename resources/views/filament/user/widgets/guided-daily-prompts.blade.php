<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Daily Reflection</h3>
                <span class="text-sm text-gray-500">{{ now()->format('M j, Y') }}</span>
            </div>

            @if($this->hasEntryToday())
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    You've already logged your reflection today! Great job staying consistent.
                                </p>
                            </div>
                        </div>
                        <button
                            wire:click="openModal"
                            type="button"
                            class="fi-btn fi-btn-size-md fi-btn-color-primary relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm"
                            style="background: linear-gradient(135deg, rgb(34 197 94) 0%, rgb(59 130 246) 100%); color: white; border: none;"
                        >
                            Log Again
                        </button>
                    </div>
                </div>
            @else
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-800">
                                    Haven't logged today yet. How are you feeling?
                                </p>
                            </div>
                        </div>
                        <button
                            wire:click="openModal"
                            type="button"
                            class="fi-btn fi-btn-size-md fi-btn-color-primary relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm"
                            style="background: linear-gradient(135deg, rgb(59 130 246) 0%, rgb(139 92 246) 100%); color: white; border: none;"
                        >
                            Quick Log
                        </button>
                    </div>
                </div>
            @endif

            <!-- Daily Prompts -->
            <div class="space-y-3">
                <h4 class="text-md font-medium text-gray-700 dark:text-gray-300">Today's Reflection Prompts</h4>

                <div class="grid gap-3">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-lg p-3">
                        <p class="text-sm text-blue-800 font-medium">🧠 Cognitive Focus</p>
                        <p class="text-sm text-blue-600 mt-1">How clear and focused is your thinking today?</p>
                    </div>

                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-100 rounded-lg p-3">
                        <p class="text-sm text-green-800 font-medium">😊 Mood & Wellbeing</p>
                        <p class="text-sm text-green-600 mt-1">Rate your overall mood and emotional state.</p>
                    </div>

                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-100 rounded-lg p-3">
                        <p class="text-sm text-yellow-800 font-medium">⚡ Energy Levels</p>
                        <p class="text-sm text-yellow-600 mt-1">How energetic and motivated do you feel?</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Log Modal -->
        @if($showModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form wire:submit.prevent="saveQuickLog">
                            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                            Quick Daily Log
                                        </h3>
                                        <div class="mt-4 space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quick Note</label>
                                                <textarea
                                                    wire:model="quickContent"
                                                    rows="3"
                                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
                                                    placeholder="How are you feeling today?"
                                                ></textarea>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Focus (1-10)</label>
                                                <select wire:model="quickFocus" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                                    <option value="">Select...</option>
                                                    @for($i = 1; $i <= 10; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mood (1-10)</label>
                                                <select wire:model="quickMood" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                                    <option value="">Select...</option>
                                                    @for($i = 1; $i <= 10; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Energy (1-10)</label>
                                                <select wire:model="quickEnergy" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                                    <option value="">Select...</option>
                                                    @for($i = 1; $i <= 10; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button
                                    type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                                    style="background: linear-gradient(135deg, rgb(59 130 246) 0%, rgb(139 92 246) 100%);"
                                >
                                    Save Log
                                </button>
                                <button
                                    type="button"
                                    wire:click="closeModal"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
