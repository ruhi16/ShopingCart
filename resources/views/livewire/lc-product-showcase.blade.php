<div class="bg-gray-100">
    <div class="container mx-auto">

        <nav class="bg-white shadow-sm mb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Left side - Title/Logo -->
                    <div class="flex items-center">
                        <a href="" class="text-xl font-semibold text-gray-900">
                            {{ config('app.name', 'Laravel') }}
                        </a>
                    </div>

                    <!-- Right side - User & Cart -->
                    <div class="flex items-center space-x-4">
                        <!-- Cart with badge -->
                        <div class="relative">
                            <a href="" class="text-gray-500 hover:text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <!-- Badge -->
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ $cartCount ?? 0 }}
                                </span>
                            </a>
                        </div>

                        <!-- User profile -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-700">
                                {{ Auth::user()->name }}
                            </span>
                            <!-- Profile image -->
                            <div class="relative">
                                @if(Auth::user()->profile_photo_path)
                                    <img class="h-8 w-8 rounded-full object-cover" 
                                    src="{{ asset(Auth::user()->profile_photo_path) }}" 
                                    alt="{{ Auth::user()->name }}">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <h2 class="text-4xl font-extrabold text-gray-800 text-center mb-12 animate-fade-in">Our Featured Products</h2>

        <!-- Product Grid Container -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            

            
            @foreach($products as $product)
            
                <!-- Product Card 2 -->
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden group">
                    <div class="relative w-full h-48 sm:h-56 overflow-hidden">
                        <img src="https://placehold.co/600x400/{{ $product->color_bk }}/244D4D?text=Product+{{ $product->id }}" 
                            alt="Wireless Headphones" 
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                            onerror="this.onerror=null;this.src='https://placehold.co/600x400/CCCCCC/333333?text=Image+Not+Found';">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-4">
                            <span class="text-white text-lg font-bold">{{ $product->tag ?? 'X' }}</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2 truncate">{{ $product->item->name ?? 'X' }}-{{ $product->category->name ?? 'X' }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            {{ $product->item->description ?? 'No Description'}}
                        </p>
                        
                        <div class="flex flex-col spacey-4 gap-4">
                        <div class="flex flex-col sm:flex-row items-center justify-between mt-4">
                            <div class="flex items-center space-x-2">
                                <!-- Integrated Quantity Control -->
                                <div class="flex items-center border border-gray-300 rounded-md overflow-hidden">
                                    <button onclick="changeQuantity(this, -1)" class="px-3 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">-</button>
                                    <input type="number" min="1" value="1" readonly class="w-16 text-center text-base py-1 px-0 border-none focus:outline-none focus:ring-0 bg-white">
                                    <button onclick="changeQuantity(this, 1)" class="px-3 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">+</button>
                                </div>
                            </div>
                            <span class="text-2xl font-bold text-green-600 mb-3 sm:mb-0">₹ {{ $product->price }}</span>
                        </div>

                        <button class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 shadow-md">Add to Cart</button>
                        </div>
                    </div>
                </div>


            @endforeach
            <!-- Add more product cards here following the same structure -->

        </div>
    </div>



</div>
