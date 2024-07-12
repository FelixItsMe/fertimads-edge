<div {!! $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-row justify-between']) !!}>
    <div>
        {{ $slot }}
    </div>
    <div class="flex items-center">
        <i class="fa-solid fa-globe p-3 bg-primary text-white rounded-lg"></i>
    </div>
</div>
