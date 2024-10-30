{{-- Export Modal --}}
<x-modal name="export-data" :show="$errors->isNotEmpty()" style="z-index: 9999;" focusable>
    <form method="get" action="{{ route('device.create') }}" class="p-6">
        <!-- Modal body -->
        <div class="p-4 md:p-5">
            <p class="text-gray-500 dark:text-gray-400 mb-4">Pilih Tipe Perangkat IoT:</p>
            <ul class="space-y-4 mb-4 max-h-96 overflow-y-scroll pr-2">
                @foreach ($deviceTypes as $deviceType)
                    <li>
                        <input type="radio" id="type-{{ $deviceType->id }}" name="type" value="{{ $deviceType->id }}"
                            class="hidden peer" required />
                        <label for="type-{{ $deviceType->id }}"
                            class="inline-flex items-center justify-between w-full p-5 text-gray-900 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-500 dark:peer-checked:text-blue-500 peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-900 hover:bg-gray-100 dark:text-white dark:bg-gray-600 dark:hover:bg-gray-500">
                            <div class="block">
                                <div class="w-full text-lg font-semibold">{{ $deviceType->name }}</div>
                            </div>
                            <svg class="w-4 h-4 ms-3 rtl:rotate-180 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9" />
                            </svg>
                        </label>
                    </li>
                @endforeach
            </ul>
            <button type="submit"
                class="text-white inline-flex w-full justify-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Selanjutnya
            </button>
        </div>
    </form>
</x-modal>
