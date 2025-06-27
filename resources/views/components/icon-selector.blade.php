@props(['selected' => null, 'name' => 'icon'])

<div x-data="{ open: false, selected: '{{ $selected }}' }" class="relative">
    <label class="block text-sm font-medium text-gray-700 mb-2">Category Icon</label>
    
    <!-- Selected Icon Display -->
    <button @click="open = !open" type="button" 
            class="w-full flex items-center justify-between px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        <div class="flex items-center">
            <i :class="selected ? 'fas fa-' + selected : 'fas fa-question'" class="w-5 h-5 mr-2 text-gray-600"></i>
            <span x-text="selected || 'Select an icon'" class="text-gray-900"></span>
        </div>
        <i class="fas fa-chevron-down text-gray-400"></i>
    </button>
    
    <!-- Hidden Input -->
    <input type="hidden" :name="'{{ $name }}'" :value="selected">
    
    <!-- Icon Grid -->
    <div x-show="open" @click.away="open = false" x-transition
         class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
        <div class="grid grid-cols-6 gap-2 p-3">
            @php
            $icons = [
                'wallet', 'credit-card', 'coins', 'dollar-sign', 'piggy-bank', 'chart-line',
                'briefcase', 'building', 'university', 'home', 'car', 'gas-pump',
                'utensils', 'coffee', 'shopping-cart', 'shopping-bag', 'gift', 'gamepad',
                'film', 'music', 'book', 'graduation-cap', 'dumbbell', 'heartbeat',
                'plane', 'train', 'bus', 'bicycle', 'taxi', 'ship',
                'mobile-alt', 'wifi', 'bolt', 'fire', 'tint', 'leaf',
                'tools', 'wrench', 'hammer', 'paint-brush', 'cut', 'clipboard',
                'star', 'heart', 'thumbs-up', 'award', 'trophy', 'medal'
            ];
            @endphp
            
            @foreach($icons as $icon)
            <button type="button" @click="selected = '{{ $icon }}'; open = false"
                    class="p-2 rounded hover:bg-gray-100 focus:outline-none focus:bg-blue-100 transition-colors"
                    :class="{ 'bg-blue-100 ring-2 ring-blue-500': selected === '{{ $icon }}' }">
                <i class="fas fa-{{ $icon }} text-gray-600"></i>
            </button>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush