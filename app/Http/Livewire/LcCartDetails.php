<?php

namespace App\Http\Livewire;

use Livewire\Component;

class LcCartDetails extends Component
{
    public $activeMenu = 'products';
    public $activeSubMenu = 'products';

    public $cartItemsCount = 0;
    public $cartTotal = 100.00;
    
    // Add cart items data
    public $cartItems = [
        [
            'id' => 1,
            'name' => 'Premium Headphones',
            'price' => 199.99,
            'quantity' => 1,
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&h=100&q=80'
        ],
        [
            'id' => 2,
            'name' => 'Wireless Keyboard',
            'price' => 59.99,
            'quantity' => 2,
            'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&h=100&q=80'
        ],
        [
            'id' => 2,
            'name' => 'Wireless Keyboard',
            'price' => 59.99,
            'quantity' => 2,
            'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&h=100&q=80'
        ],
        [
            'id' => 1,
            'name' => 'Premium Headphones',
            'price' => 199.99,
            'quantity' => 1,
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&h=100&q=80'
        ],
        [
            'id' => 2,
            'name' => 'Wireless Keyboard',
            'price' => 59.99,
            'quantity' => 2,
            'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&h=100&q=80'
        ],
        [
            'id' => 2,
            'name' => 'Wireless Keyboard',
            'price' => 59.99,
            'quantity' => 2,
            'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&h=100&q=80'
        ],
    ];

    public $menuItems = [
        'dashboard' => [
            'label' => 'Dashboard',
            'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        ],
        'products' => [
            'label' => 'Products',
            'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            'submenu' => [
                'all-products' => 'All Products',
                'categories' => 'Categories',
                'inventory' => 'Inventory',
            ],
        ],
        'cart' => [  // Added cart menu item
            'label' => 'Cart',
            'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
            'badge' => true,  // Indicates this item has a badge
        ],
        'orders' => [
            'label' => 'Orders',
            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        ],
    ];

    public $products = [
    [
        'id' => 1,
        'name' => 'Premium Wireless Headphones',
        'price' => 199.99,
        'description' => 'Noise-cancelling over-ear headphones with 30-hour battery life',
        'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&h=500&q=80',
        'rating' => 4.8,
        'review_count' => 124,
        'colors' => ['Black', 'Silver', 'Blue'],
        'in_stock' => true
    ],
    [
        'id' => 2,
        'name' => 'Mechanical Keyboard',
        'price' => 89.99,
        'description' => 'RGB backlit mechanical keyboard with customizable switches',
        'image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&h=500&q=80',
        'rating' => 4.6,
        'review_count' => 89,
        'colors' => ['Black', 'White'],
        'in_stock' => true
    ],
    [
        'id' => 3,
        'name' => '4K Ultra HD Monitor',
        'price' => 349.99,
        'description' => '27-inch 4K monitor with HDR and 99% sRGB coverage',
        'image' => 'https://images.unsplash.com/photo-1546538915-a9e2c8d0a8e6?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&h=500&q=80',
        'rating' => 4.9,
        'review_count' => 215,
        'colors' => ['Black'],
        'in_stock' => true
    ],
    [
        'id' => 4,
        'name' => 'Wireless Gaming Mouse',
        'price' => 59.99,
        'description' => 'High-precision wireless mouse with customizable DPI settings',
        'image' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&h=500&q=80',
        'rating' => 4.5,
        'review_count' => 76,
        'colors' => ['Black', 'Red'],
        'in_stock' => false
    ],
];

    // Calculate cart total
    public function getCartTotalProperty()
    {
        return array_reduce($this->cartItems, function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
    }

    // Calculate cart items count
    public function getCartItemsCountProperty()
    {
        return count($this->cartItems);
    }

    // Update item quantity
    public function updateQuantity($index, $action)
    {
        if ($action === 'increment') {
            $this->cartItems[$index]['quantity']++;
        } elseif ($action === 'decrement' && $this->cartItems[$index]['quantity'] > 1) {
            $this->cartItems[$index]['quantity']--;
        }
    }

    // Remove item from cart
    public function removeItem($index)
    {
        unset($this->cartItems[$index]);
        $this->cartItems = array_values($this->cartItems); // Reindex array
    }

    public function setActiveMenu($menu, $subMenu = null)
    {
        $this->activeMenu = $menu;
        $this->activeSubMenu = $subMenu;
    }
// Add product to cart
public function addToCart($productId)
{
    $product = collect($this->products)->firstWhere('id', $productId);
    
    // Check if product already in cart
    $existingIndex = collect($this->cartItems)->search(function ($item) use ($productId) {
        return $item['id'] == $productId;
    });
    
    if ($existingIndex !== false) {
        // Increment quantity if already in cart
        $this->cartItems[$existingIndex]['quantity']++;
    } else {
        // Add new item to cart
        $this->cartItems[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => 1,
            'image' => $product['image']
        ];
    }
    
    // Update active menu to show cart
    $this->activeMenu = 'cart';
    $this->activeSubMenu = null;
    
    // Small notification effect
    $this->dispatchBrowserEvent('product-added', [
        'message' => $product['name'] . ' added to cart'
    ]);
}

    public function render()
    {
        return view('livewire.lc-cart-details');
    }
}
