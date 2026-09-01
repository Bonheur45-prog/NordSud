<div class="fi-page-header-main-ctn">
    <div class="fi-section rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">
                    Public Press listing page
                </p>
                <h2 class="mt-1 text-2xl font-semibold text-gray-950 dark:text-white">
                    {{ $title }}
                </h2>
            </div>

            @if (! empty($actions))
                <div class="flex items-center gap-2">
                    @foreach ($actions as $action)
                        {{ $action }}
                    @endforeach
                </div>
            @endif
        </div>

        <div class="border-t border-gray-200 px-6 py-5 dark:border-gray-700">
            @if ($publicListingHeroImage)
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-800">
                    <img
                        src="{{ asset('storage/' . $publicListingHeroImage) }}"
                        alt="Public press listing page hero"
                        class="h-52 w-full object-cover"
                    >
                </div>
            @else
                <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                    No public press listing hero image is set on a published article yet.
                </div>
            @endif
        </div>
    </div>
</div>
