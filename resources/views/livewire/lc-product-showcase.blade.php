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
                        
                        @livewire('lc-cart')
                        
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

                        <button 
                            wire:click="addToCart({{ $product->id }})"
                            class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 shadow-md">
                            @if($curtProducts->where('id', $product->id)->count() == 0)
                                Add to Cart
                            @else
                                Product Added
                            @endif
                        {{-- Add to Cart {{ $user->name }} --}}
                        </button>
                        </div>
                    </div>
                </div>


            @endforeach
            <!-- Add more product cards here following the same structure -->

        </div>
    </div>

    <script>
        
        // Function to change quantity in the quantity control
        function changeQuantity(button, change) {
            const quantityInput = button.parentElement.querySelector('input[type="number"]');
            let currentValue = parseInt(quantityInput.value) || 1;
            let newValue = currentValue + change;
            
            // Ensure minimum quantity is 1
            if (newValue < 1) {
                newValue = 1;
            }
            
            // Set maximum quantity limit (optional)
            const maxQuantity = parseInt(quantityInput.getAttribute('max')) || 99;
            if (newValue > maxQuantity) {
                newValue = maxQuantity;
            }
            
            quantityInput.value = newValue;
            
            // Trigger input event for any listeners
            quantityInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        // Function to add item to cart (works with Livewire)
        function addToCart(productId, button) {
            // Get quantity from the quantity input in the same product card
            const productCard = button.closest('.bg-white');
            const quantityInput = productCard.querySelector('input[type="number"]');
            const quantity = parseInt(quantityInput.value) || 1;
            
            // Disable button during request
            const originalText = button.textContent;
            button.disabled = true;
            button.textContent = 'Adding...';
            button.classList.add('opacity-50');
            
            // Call Livewire method
            if (typeof Livewire !== 'undefined') {
                Livewire.emit('addToCart', productId, quantity);
            } else {
                // Fallback for direct AJAX call if Livewire is not available
                fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Item added to cart!', 'success');
                        updateCartBadge(data.cartCount);
                    } else {
                        showNotification('Failed to add item to cart', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred', 'error');
                });
            }
            
            // Re-enable button after a short delay
            setTimeout(() => {
                button.disabled = false;
                button.textContent = originalText;
                button.classList.remove('opacity-50');
            }, 1000);
        }

        // Function to update cart badge count
        function updateCartBadge(count) {
            const badge = document.querySelector('.bg-red-500');
            if (badge) {
                badge.textContent = count;
                
                // Add animation effect
                badge.classList.add('animate-pulse');
                setTimeout(() => {
                    badge.classList.remove('animate-pulse');
                }, 1000);
            }
        }

        // Function to show notification messages
        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotification = document.querySelector('.notification');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full`;
            
            // Set styles based on type
            switch (type) {
                case 'success':
                    notification.classList.add('bg-green-500', 'text-white');
                    break;
                case 'error':
                    notification.classList.add('bg-red-500', 'text-white');
                    break;
                case 'warning':
                    notification.classList.add('bg-yellow-500', 'text-black');
                    break;
                default:
                    notification.classList.add('bg-blue-500', 'text-white');
            }
            
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }, 3000);
        }

        // Function to handle image load errors
        function handleImageError(img) {
            img.onerror = null; // Prevent infinite loop
            img.src = 'https://placehold.co/600x400/CCCCCC/333333?text=Image+Not+Found';
        }

        // Function to initialize product cards
        function initializeProductCards() {
            // Add event listeners to all "Add to Cart" buttons
            document.querySelectorAll('.bg-green-500').forEach(button => {
                if (button.textContent.trim() === 'Add to Cart') {
                    button.addEventListener('click', function() {
                        // Extract product ID from the product card
                        const productCard = this.closest('.bg-white');
                        const productId = productCard.getAttribute('data-product-id') || 
                                        extractProductIdFromImage(productCard);
                        
                        if (productId) {
                            addToCart(productId, this);
                        } else {
                            showNotification('Product ID not found', 'error');
                        }
                    });
                }
            });
            
            // Add event listeners to quantity inputs for validation
            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.addEventListener('change', function() {
                    let value = parseInt(this.value);
                    if (isNaN(value) || value < 1) {
                        this.value = 1;
                    }
                });
            });
        }

        // Helper function to extract product ID from image src
        function extractProductIdFromImage(productCard) {
            const img = productCard.querySelector('img');
            if (img && img.src) {
                const match = img.src.match(/Product\+(\d+)/);
                return match ? match[1] : null;
            }
            return null;
        }

        // Livewire event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize product cards
            initializeProductCards();
            
            // Listen for Livewire events if available
            if (typeof Livewire !== 'undefined') {
                // Listen for cart updated event
                Livewire.on('cartUpdated', (data) => {
                    updateCartBadge(data.count);
                    showNotification('Cart updated successfully!', 'success');
                });
                
                // Listen for cart add success
                Livewire.on('cartAddSuccess', (data) => {
                    updateCartBadge(data.cartCount);
                    showNotification(`${data.productName} added to cart!`, 'success');
                });
                
                // Listen for cart add error
                Livewire.on('cartAddError', (message) => {
                    showNotification(message, 'error');
                });
                
                // Re-initialize after Livewire updates
                Livewire.hook('message.processed', (message, component) => {
                    initializeProductCards();
                });
            }
        });

        // Optional: Keyboard navigation for quantity controls
        document.addEventListener('keydown', function(e) {
            if (e.target.type === 'number' && e.target.closest('.flex.items-center.border')) {
                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const plusButton = e.target.nextElementSibling;
                    if (plusButton) {
                        changeQuantity(plusButton, 1);
                    }
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const minusButton = e.target.previousElementSibling;
                    if (minusButton) {
                        changeQuantity(minusButton, -1);
                    }
                }
            }
        });

        // Export functions for external use (if needed)
        if (typeof module !== 'undefined' && module.exports) {
            module.exports = {
                changeQuantity,
                addToCart,
                updateCartBadge,
                showNotification,
                handleImageError,
                initializeProductCards
            };
        }
    </script>


</div>
