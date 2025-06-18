<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="flex flex-col max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    You're logged in!
                </div>
            </div> --}}
            
            {{-- @livewire('lc-counter') --}}
            {{-- @livewire('lc-category')
            @livewire('lc-item')    --}}
            {{-- @livewire('lc-product')  --}}
            {{-- @livewire('lc-unit') --}}
            {{-- @livewire('lc-purchase') --}}
            {{-- @livewire('lc-purchase-new-entry') --}}
            {{-- @livewire('lc-purchase-new-entry-v2') --}}

            @livewire('lc-product-showcase')
            



        </div>
    </div>

   
</x-app-layout>
