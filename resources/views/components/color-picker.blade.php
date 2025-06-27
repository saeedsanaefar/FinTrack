@props(['selected' => '#3B82F6', 'name' => 'color'])

<div x-data="{ open: false, selected: '{{ $selected }}' }" class="relative">
    <label class="block text-sm font-medium text-gray-700 mb-2">Category Color</label>
    
    <!-- Selected Color Display -->
    <button @click="open = !open" type="button" 
            class="w-full flex items-center justify-between px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        <div class="flex items-center">
            <div :style="'background-color: ' + selected" class="w-6 h-6 rounded-full border-2 border-gray-300 mr-3"></div>
            <span x-text="selected" class="text-gray-900 font-mono text-sm"></span>
        </div>
        <i class="fas fa-chevron-down text-gray-400"></i>
    </button>
    
    <!-- Hidden Input -->
    <input type="hidden" :name="'{{ $name }}'" :value="selected">
    
    <!-- Color Grid -->
    <div x-show="open" @click.away="open = false" x-transition
         class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg p-4">
        
        <!-- Predefined Colors -->
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Predefined Colors</h4>
            <div class="grid grid-cols-8 gap-2">
                @php
                $colors = [
                    '#EF4444', '#F97316', '#F59E0B', '#EAB308', '#84CC16', '#22C55E',
                    '#10B981', '#14B8A6', '#06B6D4', '#0EA5E9', '#3B82F6', '#6366F1',
                    '#8B5CF6', '#A855F7', '#D946EF', '#EC4899', '#F43F5E', '#64748B',
                    '#6B7280', '#374151', '#1F2937', '#111827', '#7C2D12', '#92400E',
                    '#B45309', '#A16207', '#4D7C0F', '#166534', '#065F46', '#134E4A',
                    '#155E75', '#1E40AF', '#3730A3', '#5B21B6', '#7C3AED', '#BE185D'
                ];
                @endphp
                
                @foreach($colors as $color)
                <button type="button" @click="selected = '{{ $color }}'; open = false"
                        style="background-color: {{ $color }}"
                        class="w-8 h-8 rounded-full border-2 border-gray-300 hover:scale-110 transition-transform focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        :class="{ 'ring-2 ring-offset-2 ring-gray-800': selected === '{{ $color }}' }">
                </button>
                @endforeach
            </div>
        </div>
        
        <!-- Custom Color Input -->
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-2">Custom Color</h4>
            <div class="flex items-center space-x-2">
                <input type="color" x-model="selected" 
                       class="w-12 h-8 border border-gray-300 rounded cursor-pointer">
                <input type="text" x-model="selected" 
                       class="flex-1 px-3 py-1 border border-gray-300 rounded-md text-sm font-mono focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="#000000">
            </div>
        </div>
    </div>
</div>