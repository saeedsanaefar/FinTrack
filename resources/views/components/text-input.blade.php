@props(['disabled' => false, 'icon' => null, 'helper' => null])

<div class="relative">
    @if($icon)
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="{{ $icon }} text-gray-400 text-sm"></i>
        </div>
    @endif
    
    <input @disabled($disabled) 
           {{ $attributes->merge([
               'class' => 'block w-full ' . 
                         ($icon ? 'pl-10 ' : '') . 
                         'pr-3 py-2.5 border border-gray-600 rounded-lg shadow-sm ' .
                         'placeholder-gray-400 text-gray-100 ' .
                         'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 ' .
                         'transition duration-200 ease-in-out ' .
                         'hover:border-gray-500 ' .
                         ($disabled ? 'bg-gray-800 text-gray-500 cursor-not-allowed' : 'bg-gray-800')
           ]) }}>
    
    @if($helper)
        <p class="mt-1 text-sm text-gray-500">{{ $helper }}</p>
    @endif
</div>
