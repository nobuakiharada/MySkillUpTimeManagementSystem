@props(['color' => 'bg-gray-200 text-black hover:bg-gray-300 focus:bg-gray-400'])

<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => "inline-flex items-center justify-center px-6 py-3 text-lg border border-transparent rounded-md font-semibold uppercase tracking-wide focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 $color"
]) }}>
    {{ $slot }}
</button>