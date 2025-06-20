<div>xx
    @if($activeMenu === 'cart')
        <!-- Enhanced Cart Section -->
        <div class="max-w-4xl mx-auto">cart
            <!-- Cart Header -->
            <div class="bg-white rounded-t-lg shadow-sm p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <h2 class="ml-3 text-2xl font-semibold text-gray-800">Your Shopping Cart</h2>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                        {{ $cartItemsCount }} {{ Str::plural('item', $cartItemsCount) }}
                    </span>
                </div>
            </div>

            @if($cartItemsCount > 0)
            <!-- Cart Items -->
            <div class="bg-white divide-y divide-gray-100">
                @foreach($cartItems as $index => $item)
                <div class="p-5 hover:bg-gray-50 transition-colors duration-150">
                    <div class="flex flex-col sm:flex-row">
                        <!-- Product Image -->
                        <div class="flex-shrink-0 mb-4 sm:mb-0">
                            <img class="h-24 w-24 rounded-lg object-cover border border-gray-200" src="{{ $item['image'] }}" alt="{{ $item['name'] }}" loading="lazy">
                        </div>

                        <!-- Product Details -->
                        <div class="ml-0 sm:ml-4 flex-1">
                            <div class="flex justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-800">{{ $item['name'] }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">SKU: {{ 'PROD-' . str_pad($item['id'], 4, '0', STR_PAD_LEFT) }}</p>
                                    <div class="mt-2 flex items-center">
                                        <span class="text-sm text-gray-500">In Stock</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Price and Remove -->
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900">${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                                    <button wire:click="removeItem({{ $index }})" class="mt-2 text-sm text-red-500 hover:text-red-700 flex items-center justify-end">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Remove
                                    </button>
                                </div>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center border border-gray-200 rounded-md">
                                    <button wire:click="updateQuantity({{ $index }}, 'decrement')" class="px-3 py-1 text-gray-600 hover:bg-gray-100 transition-colors" :disabled="{{ $item['quantity'] <= 1 ? 'true' : 'false' }}" :class="{ 'text-gray-300 cursor-not-allowed': {{ $item['quantity'] <= 1 ? 'true' : 'false' }} }">
                                        −
                                    </button>
                                    <span class="px-4 py-1 border-x border-gray-200 bg-white text-center w-12">
                                        {{ $item['quantity'] }}
                                    </span>
                                    <button wire:click="updateQuantity({{ $index }}, 'increment')" class="px-3 py-1 text-gray-600 hover:bg-gray-100 transition-colors">
                                        +
                                    </button>
                                </div>

                                <div class="text-sm text-gray-500">
                                    ${{ number_format($item['price'], 2) }} each
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div class="bg-gray-50 p-6 rounded-b-lg border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">${{ number_format($cartTotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium">Free</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax</span>
                        <span class="font-medium">Calculated at checkout</span>
                    </div>
                </div>

                <div class="mt-6 border-t border-gray-200 pt-4">
                    <div class="flex justify-between text-lg font-bold text-gray-900">
                        <span>Total</span>
                        <span>${{ number_format($cartTotal, 2) }}</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <button wire:click="setActiveMenu('products')" class="flex items-center justify-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                        </svg>
                        Continue Shopping
                    </button>
                    <button class="flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Checkout
                    </button>
                </div>

                <p class="mt-4 text-center text-sm text-gray-500">
                    or <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Add more items</a>
                </p>
            </div>
            @else
            <!-- Empty Cart State -->
            <div class="bg-white rounded-b-lg p-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-4 text-xl font-medium text-gray-900">Your cart is empty</h3>
                <p class="mt-2 text-gray-500">Looks like you haven't added any items to your cart yet</p>
                <div class="mt-6">
                    <button wire:click="setActiveMenu('products')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Browse Products
                    </button>
                </div>
            </div>
            @endif
        </div>
    xx
    @elseif($activeMenu === 'products')
        <!-- Product Showcase Section -->
        <div class="max-w-7xl mx-auto">product
            <!-- Header -->
            <div class="bg-white rounded-t-lg shadow-sm p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Our Products</h2>
                        <p class="mt-1 text-gray-600">Browse our latest collection</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <div class="relative">
                            <input type="text" placeholder="Search products..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="bg-white p-6 rounded-b-lg">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                    <div class="group border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200">
                        <!-- Product Image -->
                        <div class="relative aspect-square bg-gray-100">
                            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="w-full h-full object-cover" loading="lazy">
                            <!-- Quick Add to Cart (appears on hover) -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <button wire:click="addToCart({{ $product['id'] }})" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 transition-colors duration-200 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Add to Cart
                                </button>
                            </div>
                            <!-- Out of Stock Badge -->
                            @if(!$product['in_stock'])
                            <span class="absolute top-2 right-2 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                Out of Stock
                            </span>
                            @endif
                        </div>

                        <!-- Product Details -->
                        <div class="p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $product['name'] }}</h3>
                                    <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ $product['description'] }}</p>
                                </div>
                                <span class="font-semibold text-gray-900">${{ number_format($product['price'], 2) }}</span>
                            </div>

                            <!-- Rating -->
                            <div class="mt-3 flex items-center">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++) 
                                        @if($i <=floor($product['rating'])) <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @elseif($i - 0.5 <= $product['rating']) <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endif
                                    @endfor
                                    <span class="ml-1 text-xs text-gray-500">{{ $product['rating'] }} ({{ $product['review_count'] }})</span>
                                </div>
                            </div>

                            <!-- Color Options -->
                            <div class="mt-3">
                                <h4 class="text-xs text-gray-500">Colors</h4>
                                <div class="mt-1 flex space-x-1">
                                    @foreach($product['colors'] as $color)
                                        <span class="w-4 h-4 rounded-full border border-gray-200" style="background-color: {{ strtolower($color) }}" title="{{ $color }}"></span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Add to Cart Button (mobile) -->
                            <div class="mt-4 sm:hidden">
                                <button wire:click="addToCart({{ $product['id'] }})" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center" {{ !$product['in_stock'] ? 'disabled' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination (placeholder) -->
                <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-4">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">1</span> to <span class="font-medium">4</span> of <span class="font-medium">12</span> results
                            </p>
                        </div>

                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a href="#" aria-current="page" class="z-10 bg-blue-50 border-blue-500 text-blue-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    1
                                </a>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    2
                                </a>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    3
                                </a>
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </nav>
                        </div>


                    </div>
                </div>

                
            </div>
        </div>
    @else
    otherwise
    <!-- Other content sections -->
    <!-- ... -->
    <!-- Original Content Area -->
    <!-- ... (keep your existing non-cart content here) ... -->
    @endif



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('product-added', (data) => {
                // Create notification
                const notification = document.createElement('div');
                notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg flex items-center animate-fade-in-up';
                notification.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    ${data.message}
                `;
                
                document.body.appendChild(notification);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    notification.classList.remove('animate-fade-in-up');
                    notification.classList.add('animate-fade-out');
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            });
        });
    </script>

</div>
