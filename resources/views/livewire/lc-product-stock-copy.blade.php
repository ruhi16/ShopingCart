<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Maintenance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .profit-positive { color: #059669; }
        .profit-negative { color: #dc2626; }
        .category-electronics { background-color: #dbeafe; }
        .category-clothing { background-color: #fef3c7; }
        .category-home { background-color: #d1fae5; }
        .category-books { background-color: #fce7f3; }
    </style>
</head>
<body class="bg-gray-50 p-4">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                <h1 class="text-2xl font-bold text-white">Stock Maintenance System</h1>
                <p class="text-blue-100 mt-1">Comprehensive inventory tracking with profit analysis</p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-4 gap-4 p-6 bg-gray-50 border-b">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Total Items</h3>
                    <p class="text-2xl font-bold text-gray-900">24</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Total Investment</h3>
                    <p class="text-2xl font-bold text-blue-600">₹1,24,500</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Expected Revenue</h3>
                    <p class="text-2xl font-bold text-green-600">₹1,68,200</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Avg. Profit Margin</h3>
                    <p class="text-2xl font-bold text-purple-600">35.1%</p>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="p-6 border-b bg-white">
                <div class="flex flex-wrap gap-4 items-center">
                    <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <option>All Categories</option>
                        <option>Electronics</option>
                        <option>Clothing</option>
                        <option>Home & Garden</option>
                        <option>Books</option>
                    </select>
                    <input type="text" placeholder="Search by product name..." class="border border-gray-300 rounded-md px-3 py-2 text-sm flex-1 max-w-xs">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">
                        Add New Item
                    </button>
                </div>
            </div>

            <!-- Main Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Invoice #</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Category</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Product Name</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Qty</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Purchase Rate</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Total Cost</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Selling Price</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Total Revenue</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Profit/Unit</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Profit %</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Stock Status</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <!-- Electronics -->
                        <tr class="hover:bg-gray-50 category-electronics">
                            <td class="px-4 py-3 font-mono text-xs">INV-2024-001</td>
                            <td class="px-4 py-3">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">Electronics</span>
                            </td>
                            <td class="px-4 py-3 font-medium">iPhone 15 Pro</td>
                            <td class="px-4 py-3 text-center">5</td>
                            <td class="px-4 py-3 text-right">₹85,000</td>
                            <td class="px-4 py-3 text-right font-medium">₹4,25,000</td>
                            <td class="px-4 py-3 text-right">₹1,20,000</td>
                            <td class="px-4 py-3 text-right font-medium">₹6,00,000</td>
                            <td class="px-4 py-3 text-right profit-positive">₹35,000</td>
                            <td class="px-4 py-3 text-right profit-positive font-semibold">41.2%</td>
                            <td class="px-4 py-3 text-center">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">In Stock</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                                <button class="text-red-600 hover:text-red-800">Delete</button>
                            </td>
                        </tr>
                        
                        <tr class="hover:bg-gray-50 category-electronics">
                            <td class="px-4 py-3 font-mono text-xs">INV-2024-002</td>
                            <td class="px-4 py-3">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">Electronics</span>
                            </td>
                            <td class="px-4 py-3 font-medium">MacBook Air M2</td>
                            <td class="px-4 py-3 text-center">3</td>
                            <td class="px-4 py-3 text-right">₹95,000</td>
                            <td class="px-4 py-3 text-right font-medium">₹2,85,000</td>
                            <td class="px-4 py-3 text-right">₹1,25,000</td>
                            <td class="px-4 py-3 text-right font-medium">₹3,75,000</td>
                            <td class="px-4 py-3 text-right profit-positive">₹30,000</td>
                            <td class="px-4 py-3 text-right profit-positive font-semibold">31.6%</td>
                            <td class="px-4 py-3 text-center">
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">Low Stock</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                                <button class="text-red-600 hover:text-red-800">Delete</button>
                            </td>
                        </tr>

                        <!-- Clothing -->
                        <tr class="hover:bg-gray-50 category-clothing">
                            <td class="px-4 py-3 font-mono text-xs">INV-2024-003</td>
                            <td class="px-4 py-3">
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">Clothing</span>
                            </td>
                            <td class="px-4 py-3 font-medium">Designer Jeans</td>
                            <td class="px-4 py-3 text-center">20</td>
                            <td class="px-4 py-3 text-right">₹1,200</td>
                            <td class="px-4 py-3 text-right font-medium">₹24,000</td>
                            <td class="px-4 py-3 text-right">₹2,500</td>
                            <td class="px-4 py-3 text-right font-medium">₹50,000</td>
                            <td class="px-4 py-3 text-right profit-positive">₹1,300</td>
                            <td class="px-4 py-3 text-right profit-positive font-semibold">108.3%</td>
                            <td class="px-4 py-3 text-center">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">In Stock</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                                <button class="text-red-600 hover:text-red-800">Delete</button>
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50 category-clothing">
                            <td class="px-4 py-3 font-mono text-xs">INV-2024-004</td>
                            <td class="px-4 py-3">
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">Clothing</span>
                            </td>
                            <td class="px-4 py-3 font-medium">Cotton T-Shirts</td>
                            <td class="px-4 py-3 text-center">50</td>
                            <td class="px-4 py-3 text-right">₹300</td>
                            <td class="px-4 py-3 text-right font-medium">₹15,000</td>
                            <td class="px-4 py-3 text-right">₹599</td>
                            <td class="px-4 py-3 text-right font-medium">₹29,950</td>
                            <td class="px-4 py-3 text-right profit-positive">₹299</td>
                            <td class="px-4 py-3 text-right profit-positive font-semibold">99.7%</td>
                            <td class="px-4 py-3 text-center">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">In Stock</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                                <button class="text-red-600 hover:text-red-800">Delete</button>
                            </td>
                        </tr>

                        <!-- Home & Garden -->
                        <tr class="hover:bg-gray-50 category-home">
                            <td class="px-4 py-3 font-mono text-xs">INV-2024-005</td>
                            <td class="px-4 py-3">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Home & Garden</span>
                            </td>
                            <td class="px-4 py-3 font-medium">Air Purifier</td>
                            <td class="px-4 py-3 text-center">8</td>
                            <td class="px-4 py-3 text-right">₹12,000</td>
                            <td class="px-4 py-3 text-right font-medium">₹96,000</td>
                            <td class="px-4 py-3 text-right">₹18,999</td>
                            <td class="px-4 py-3 text-right font-medium">₹1,51,992</td>
                            <td class="px-4 py-3 text-right profit-positive">₹6,999</td>
                            <td class="px-4 py-3 text-right profit-positive font-semibold">58.3%</td>
                            <td class="px-4 py-3 text-center">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">In Stock</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                                <button class="text-red-600 hover:text-red-800">Delete</button>
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50 category-home">
                            <td class="px-4 py-3 font-mono text-xs">INV-2024-006</td>
                            <td class="px-4 py-3">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Home & Garden</span>
                            </td>
                            <td class="px-4 py-3 font-medium">LED Desk Lamp</td>
                            <td class="px-4 py-3 text-center">15</td>
                            <td class="px-4 py-3 text-right">₹800</td>
                            <td class="px-4 py-3 text-right font-medium">₹12,000</td>
                            <td class="px-4 py-3 text-right">₹1,299</td>
                            <td class="px-4 py-3 text-right font-medium">₹19,485</td>
                            <td class="px-4 py-3 text-right profit-positive">₹499</td>
                            <td class="px-4 py-3 text-right profit-positive font-semibold">62.4%</td>
                            <td class="px-4 py-3 text-center">
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Out of Stock</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                                <button class="text-red-600 hover:text-red-800">Delete</button>
                            </td>
                        </tr>

                        <!-- Books -->
                        <tr class="hover:bg-gray-50 category-books">
                            <td class="px-4 py-3 font-mono text-xs">INV-2024-007</td>
                            <td class="px-4 py-3">
                                <span class="bg-pink-100 text-pink-800 px-2 py-1 rounded-full text-xs font-medium">Books</span>
                            </td>
                            <td class="px-4 py-3 font-medium">Programming Books Set</td>
                            <td class="px-4 py-3 text-center">25</td>
                            <td class="px-4 py-3 text-right">₹450</td>
                            <td class="px-4 py-3 text-right font-medium">₹11,250</td>
                            <td class="px-4 py-3 text-right">₹799</td>
                            <td class="px-4 py-3 text-right font-medium">₹19,975</td>
                            <td class="px-4 py-3 text-right profit-positive">₹349</td>
                            <td class="px-4 py-3 text-right profit-positive font-semibold">77.6%</td>
                            <td class="px-4 py-3 text-center">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">In Stock</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                                <button class="text-red-600 hover:text-red-800">Delete</button>
                            </td>
                        </tr>

                        <!-- Additional sample rows for demonstration -->
                        <tr class="hover:bg-gray-50 category-electronics">
                            <td class="px-4 py-3 font-mono text-xs">INV-2024-008</td>
                            <td class="px-4 py-3">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">Electronics</span>
                            </td>
                            <td class="px-4 py-3 font-medium">Wireless Headphones</td>
                            <td class="px-4 py-3 text-center">12</td>
                            <td class="px-4 py-3 text-right">₹2,500</td>
                            <td class="px-4 py-3 text-right font-medium">₹30,000</td>
                            <td class="px-4 py-3 text-right">₹4,999</td>
                            <td class="px-4 py-3 text-right font-medium">₹59,988</td>
                            <td class="px-4 py-3 text-right profit-positive">₹2,499</td>
                            <td class="px-4 py-3 text-right profit-positive font-semibold">99.9%</td>
                            <td class="px-4 py-3 text-center">
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">Low Stock</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                                <button class="text-red-600 hover:text-red-800">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-6 py-4 border-t flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Showing 1-8 of 24 entries
                </div>
                <div class="flex space-x-2">
                    <button class="px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50 disabled:opacity-50" disabled>
                        Previous
                    </button>
                    <button class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">1</button>
                    <button class="px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">2</button>
                    <button class="px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">3</button>
                    <button class="px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simple interactivity for demonstration
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects and basic interactions
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.addEventListener('click', function(e) {
                    if (!e.target.matches('button')) {
                        this.classList.toggle('ring-2');
                        this.classList.toggle('ring-blue-200');
                    }
                });
            });

            // Filter functionality
            const categoryFilter = document.querySelector('select');
            categoryFilter.addEventListener('change', function() {
                const selectedCategory = this.value.toLowerCase().replace(' & ', '-').replace(/\s+/g, '-');
                rows.forEach(row => {
                    if (selectedCategory === 'all-categories' || selectedCategory === '') {
                        row.style.display = '';
                    } else {
                        const hasCategory = row.classList.contains(`category-${selectedCategory}`);
                        row.style.display = hasCategory ? '' : 'none';
                    }
                });
            });

            // Search functionality
            const searchInput = document.querySelector('input[type="text"]');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                rows.forEach(row => {
                    const productName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    const invoiceNumber = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                    const matches = productName.includes(searchTerm) || invoiceNumber.includes(searchTerm);
                    row.style.display = matches ? '' : 'none';
                });
            });
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Maintenance System with Unit Conversion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .profit-positive { color: #059669; }
        .profit-negative { color: #dc2626; }
        .category-electronics { background-color: #dbeafe; }
        .category-clothing { background-color: #fef3c7; }
        .category-home { background-color: #d1fae5; }
        .category-books { background-color: #fce7f3; }
        .category-food { background-color: #fef2f2; }
        .unit-conversion { background-color: #f0f9ff; border-left: 4px solid #0284c7; }
    </style>
</head>
<body class="bg-gray-50 p-4">
    <div class="max-w-full mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                <h1 class="text-2xl font-bold text-white">Stock Maintenance System</h1>
                <p class="text-blue-100 mt-1">Inventory tracking with unit conversion and batch pricing</p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-5 gap-4 p-6 bg-gray-50 border-b">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Total SKUs</h3>
                    <p class="text-2xl font-bold text-gray-900">18</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Total Investment</h3>
                    <p class="text-2xl font-bold text-blue-600">₹2,45,800</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Expected Revenue</h3>
                    <p class="text-2xl font-bold text-green-600">₹4,12,350</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Avg. Profit Margin</h3>
                    <p class="text-2xl font-bold text-purple-600">67.8%</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Unit Conversions</h3>
                    <p class="text-2xl font-bold text-orange-600">12</p>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="p-6 border-b bg-white">
                <div class="flex flex-wrap gap-4 items-center">
                    <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <option>All Categories</option>
                        <option>Electronics</option>
                        <option>Clothing</option>
                        <option>Home & Garden</option>
                        <option>Books</option>
                        <option>Food & Beverages</option>
                    </select>
                    <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <option>All Unit Types</option>
                        <option>Bulk to Retail</option>
                        <option>Weight Conversion</option>
                        <option>Volume Conversion</option>
                        <option>Single Unit</option>
                    </select>
                    <input type="text" placeholder="Search products..." class="border border-gray-300 rounded-md px-3 py-2 text-sm flex-1 max-w-xs">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">
                        Add New Item
                    </button>
                </div>
            </div>

            <!-- Main Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 sticky top-0">
                        <tr>
                            <th class="px-3 py-3 text-left font-semibold text-gray-700">Invoice #</th>
                            <th class="px-3 py-3 text-left font-semibold text-gray-700">Category</th>
                            <th class="px-3 py-3 text-left font-semibold text-gray-700">Product Name</th>
                            <th class="px-3 py-3 text-center font-semibold text-gray-700">Purchase Unit</th>
                            <th class="px-3 py-3 text-center font-semibold text-gray-700">Purchase Qty</th>
                            <th class="px-3 py-3 text-right font-semibold text-gray-700">Rate/Unit</th>
                            <th class="px-3 py-3 text-right font-semibold text-gray-700">Total Cost</th>
                            <th class="px-3 py-3 text-center font-semibold text-gray-700">Selling Unit</th>
                            <th class="px-3 py-3 text-center font-semibold text-gray-700">Conversion</th>
                            <th class="px-3 py-3 text-center font-semibold text-gray-700">Available Units</th>
                            <th class="px-3 py-3 text-right font-semibold text-gray-700">Cost/Sell Unit</th>
                            <th class="px-3 py-3 text-right font-semibold text-gray-700">Selling Price</th>
                            <th class="px-3 py-3 text-right font-semibold text-gray-700">Profit/Unit</th>
                            <th class="px-3 py-3 text-right font-semibold text-gray-700">Profit %</th>
                            <th class="px-3 py-3 text-center font-semibold text-gray-700">Stock Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <!-- Rice - Bulk to Retail -->
                        <tr class="hover:bg-gray-50 category-food unit-conversion">
                            <td class="px-3 py-3 font-mono text-xs">INV-2024-001</td>
                            <td class="px-3 py-3">
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Food</span>
                            </td>
                            <td class="px-3 py-3 font-medium">Basmati Rice</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-blue-50 px-2 py-1 rounded text-xs">50kg Sack</span>
                            </td>
                            <td class="px-3 py-3 text-center">10</td>
                            <td class="px-3 py-3 text-right">₹2,500</td>
                            <td class="px-3 py-3 text-right font-medium">₹25,000</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-green-50 px-2 py-1 rounded text-xs">1kg Pack</span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs">1:50</span>
                            </td>
                            <td class="px-3 py-3 text-center font-medium">500</td>
                            <td class="px-3 py-3 text-right">₹50</td>
                            <td class="px-3 py-3 text-right">₹85</td>
                            <td class="px-3 py-3 text-right profit-positive">₹35</td>
                            <td class="px-3 py-3 text-right profit-positive font-semibold">70%</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">In Stock</span>
                            </td>
                        </tr>

                        <!-- Cooking Oil - Volume Conversion -->
                        <tr class="hover:bg-gray-50 category-food unit-conversion">
                            <td class="px-3 py-3 font-mono text-xs">INV-2024-002</td>
                            <td class="px-3 py-3">
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Food</span>
                            </td>
                            <td class="px-3 py-3 font-medium">Sunflower Oil</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-blue-50 px-2 py-1 rounded text-xs">15L Tin</span>
                            </td>
                            <td class="px-3 py-3 text-center">20</td>
                            <td class="px-3 py-3 text-right">₹1,800</td>
                            <td class="px-3 py-3 text-right font-medium">₹36,000</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-green-50 px-2 py-1 rounded text-xs">1L Bottle</span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs">1:15</span>
                            </td>
                            <td class="px-3 py-3 text-center font-medium">300</td>
                            <td class="px-3 py-3 text-right">₹120</td>
                            <td class="px-3 py-3 text-right">₹180</td>
                            <td class="px-3 py-3 text-right profit-positive">₹60</td>
                            <td class="px-3 py-3 text-right profit-positive font-semibold">50%</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">In Stock</span>
                            </td>
                        </tr>

                        <!-- Fabric - Bulk to Retail -->
                        <tr class="hover:bg-gray-50 category-clothing unit-conversion">
                            <td class="px-3 py-3 font-mono text-xs">INV-2024-003</td>
                            <td class="px-3 py-3">
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">Clothing</span>
                            </td>
                            <td class="px-3 py-3 font-medium">Cotton Fabric</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-blue-50 px-2 py-1 rounded text-xs">100m Roll</span>
                            </td>
                            <td class="px-3 py-3 text-center">5</td>
                            <td class="px-3 py-3 text-right">₹8,000</td>
                            <td class="px-3 py-3 text-right font-medium">₹40,000</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-green-50 px-2 py-1 rounded text-xs">1m Length</span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs">1:100</span>
                            </td>
                            <td class="px-3 py-3 text-center font-medium">500</td>
                            <td class="px-3 py-3 text-right">₹80</td>
                            <td class="px-3 py-3 text-right">₹150</td>
                            <td class="px-3 py-3 text-right profit-positive">₹70</td>
                            <td class="px-3 py-3 text-right profit-positive font-semibold">87.5%</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">In Stock</span>
                            </td>
                        </tr>

                        <!-- Electronic Components - Pack to Individual -->
                        <tr class="hover:bg-gray-50 category-electronics unit-conversion">
                            <td class="px-3 py-3 font-mono text-xs">INV-2024-004</td>
                            <td class="px-3 py-3">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">Electronics</span>
                            </td>
                            <td class="px-3 py-3 font-medium">LED Bulbs</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-blue-50 px-2 py-1 rounded text-xs">100pc Box</span>
                            </td>
                            <td class="px-3 py-3 text-center">8</td>
                            <td class="px-3 py-3 text-right">₹5,000</td>
                            <td class="px-3 py-3 text-right font-medium">₹40,000</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-green-50 px-2 py-1 rounded text-xs">1 Piece</span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs">1:100</span>
                            </td>
                            <td class="px-3 py-3 text-center font-medium">800</td>
                            <td class="px-3 py-3 text-right">₹50</td>
                            <td class="px-3 py-3 text-right">₹99</td>
                            <td class="px-3 py-3 text-right profit-positive">₹49</td>
                            <td class="px-3 py-3 text-right profit-positive font-semibold">98%</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">In Stock</span>
                            </td>
                        </tr>

                        <!-- Books - Bulk Purchase -->
                        <tr class="hover:bg-gray-50 category-books unit-conversion">
                            <td class="px-3 py-3 font-mono text-xs">INV-2024-005</td>
                            <td class="px-3 py-3">
                                <span class="bg-pink-100 text-pink-800 px-2 py-1 rounded-full text-xs font-medium">Books</span>
                            </td>
                            <td class="px-3 py-3 font-medium">Textbook Series</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-blue-50 px-2 py-1 rounded text-xs">50pc Bundle</span>
                            </td>
                            <td class="px-3 py-3 text-center">12</td>
                            <td class="px-3 py-3 text-right">₹7,500</td>
                            <td class="px-3 py-3 text-right font-medium">₹90,000</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-green-50 px-2 py-1 rounded text-xs">1 Book</span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs">1:50</span>
                            </td>
                            <td class="px-3 py-3 text-center font-medium">600</td>
                            <td class="px-3 py-3 text-right">₹150</td>
                            <td class="px-3 py-3 text-right">₹299</td>
                            <td class="px-3 py-3 text-right profit-positive">₹149</td>
                            <td class="px-3 py-3 text-right profit-positive font-semibold">99.3%</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">In Stock</span>
                            </td>
                        </tr>

                        <!-- Wire - Length Conversion -->
                        <tr class="hover:bg-gray-50 category-electronics unit-conversion">
                            <td class="px-3 py-3 font-mono text-xs">INV-2024-006</td>
                            <td class="px-3 py-3">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">Electronics</span>
                            </td>
                            <td class="px-3 py-3 font-medium">Copper Wire</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-blue-50 px-2 py-1 rounded text-xs">100m Coil</span>
                            </td>
                            <td class="px-3 py-3 text-center">15</td>
                            <td class="px-3 py-3 text-right">₹800</td>
                            <td class="px-3 py-3 text-right font-medium">₹12,000</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-green-50 px-2 py-1 rounded text-xs">1m Length</span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs">1:100</span>
                            </td>
                            <td class="px-3 py-3 text-center font-medium">1500</td>
                            <td class="px-3 py-3 text-right">₹8</td>
                            <td class="px-3 py-3 text-right">₹15</td>
                            <td class="px-3 py-3 text-right profit-positive">₹7</td>
                            <td class="px-3 py-3 text-right profit-positive font-semibold">87.5%</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">Low Stock</span>
                            </td>
                        </tr>

                        <!-- Single Unit Items for comparison -->
                        <tr class="hover:bg-gray-50 category-electronics">
                            <td class="px-3 py-3 font-mono text-xs">INV-2024-007</td>
                            <td class="px-3 py-3">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">Electronics</span>
                            </td>
                            <td class="px-3 py-3 font-medium">Smartphone</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-gray-50 px-2 py-1 rounded text-xs">1 Unit</span>
                            </td>
                            <td class="px-3 py-3 text-center">10</td>
                            <td class="px-3 py-3 text-right">₹15,000</td>
                            <td class="px-3 py-3 text-right font-medium">₹1,50,000</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-gray-50 px-2 py-1 rounded text-xs">1 Unit</span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">1:1</span>
                            </td>
                            <td class="px-3 py-3 text-center font-medium">10</td>
                            <td class="px-3 py-3 text-right">₹15,000</td>
                            <td class="px-3 py-3 text-right">₹22,999</td>
                            <td class="px-3 py-3 text-right profit-positive">₹7,999</td>
                            <td class="px-3 py-3 text-right profit-positive font-semibold">53.3%</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">In Stock</span>
                            </td>
                        </tr>

                        <!-- Tea - Weight Conversion -->
                        <tr class="hover:bg-gray-50 category-food unit-conversion">
                            <td class="px-3 py-3 font-mono text-xs">INV-2024-008</td>
                            <td class="px-3 py-3">
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Food</span>
                            </td>
                            <td class="px-3 py-3 font-medium">Premium Tea</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-blue-50 px-2 py-1 rounded text-xs">25kg Chest</span>
                            </td>
                            <td class="px-3 py-3 text-center">4</td>
                            <td class="px-3 py-3 text-right">₹12,000</td>
                            <td class="px-3 py-3 text-right font-medium">₹48,000</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-green-50 px-2 py-1 rounded text-xs">250g Pack</span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs">1:100</span>
                            </td>
                            <td class="px-3 py-3 text-center font-medium">400</td>
                            <td class="px-3 py-3 text-right">₹120</td>
                            <td class="px-3 py-3 text-right">₹299</td>
                            <td class="px-3 py-3 text-right profit-positive">₹179</td>
                            <td class="px-3 py-3 text-right profit-positive font-semibold">149.2%</td>
                            <td class="px-3 py-3 text-center">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">In Stock</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Unit Conversion Legend -->
            <div class="bg-blue-50 p-4 border-t">
                <h3 class="text-sm font-semibold text-blue-800 mb-2">Unit Conversion Guide:</h3>
                <div class="grid grid-cols-4 gap-4 text-xs">
                    <div class="flex items-center">
                        <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs mr-2">1:50</span>
                        <span class="text-gray-600">1 Purchase Unit = 50 Selling Units</span>
                    </div>
                    <div class="flex items-center">
                        <span class="bg-blue-50 px-2 py-1 rounded text-xs mr-2">Purchase Unit</span>
                        <span class="text-gray-600">Bulk buying format</span>
                    </div>
                    <div class="flex items-center">
                        <span class="bg-green-50 px-2 py-1 rounded text-xs mr-2">Selling Unit</span>
                        <span class="text-gray-600">Retail selling format</span>
                    </div>
                    <div class="flex items-center">
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs mr-2">1:1</span>
                        <span class="text-gray-600">No unit conversion</span>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-6 py-4 border-t flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Showing 1-8 of 18 entries
                </div>
                <div class="flex space-x-2">
                    <button class="px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50 disabled:opacity-50" disabled>
                        Previous
                    </button>
                    <button class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">1</button>
                    <button class="px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">2</button>
                    <button class="px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">3</button>
                    <button class="px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enhanced interactivity for unit conversion features
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            
            // Highlight unit conversion rows
            const conversionRows = document.querySelectorAll('.unit-conversion');
            conversionRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 4px 12px rgba(59, 130, 246, 0.15)';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.boxShadow = '';
                });
            });

            // Category filter
            const categoryFilter = document.querySelector('select');
            categoryFilter.addEventListener('change', function() {
                const selectedCategory = this.value.toLowerCase().replace(' & ', '-').replace(/\s+/g, '-');
                rows.forEach(row => {
                    if (selectedCategory === 'all-categories' || selectedCategory === '') {
                        row.style.display = '';
                    } else {
                        const hasCategory = row.classList.contains(`category-${selectedCategory}`);
                        row.style.display = hasCategory ? '' : 'none';
                    }
                });
            });

            // Unit type filter
            const unitFilter = document.querySelectorAll('select')[1];
            unitFilter.addEventListener('change', function() {
                const selectedType = this.value.toLowerCase();
                rows.forEach(row => {
                    if (selectedType === 'all-unit-types' || selectedType === '') {
                        row.style.display = '';
                    } else if (selectedType === 'single-unit') {
                        row.style.display = row.classList.contains('unit-conversion') ? 'none' : '';
                    } else {
                        row.style.display = row.classList.contains('unit-conversion') ? '' : 'none';
                    }
                });
            });

            // Search functionality
            const searchInput = document.querySelector('input[type="text"]');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                rows.forEach(row => {
                    const productName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    const invoiceNumber = row.querySelector('td:nth-child(1)').textContent.
