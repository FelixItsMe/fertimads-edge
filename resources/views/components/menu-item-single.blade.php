<li {{ $attributes->merge(['class' => 'my-0.5 w-64 basis-auto flex-col m-0 p-0 list-none items-start justify-start']) }}>
    <a href="#" class="rounded-md font-semibold mx-4 text-sm px-4 py-2.5 relative flex items-center flex-shrink basis-auto m-0 hover:bg-slate-200 hover:bg-opacity-25">
        {{ $slot }}
    </a>
</li>

