<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-300 shadow-md hover:bg-gray-50 dark:hover:bg-gray-700 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 active:scale-95 transition-all duration-200 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
