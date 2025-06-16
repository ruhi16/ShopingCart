<div>
    <div class="bg-gray-100 flex items-center justify-center ">
        <div class="container mx-auto p-4">

            <div class="flex justify-around items-center mb-4 p-2 bg-green-300 rounded-lg shadow-lg">
                <h2 class="text-lg font-semibold text-gray-800">Purchase Entry Table</h2>
                {{-- <button onclick="handleAdd()" class="px-3 py-1.5 text-sm font-semibold text-white bg-green-500 rounded hover:bg-green-600 transition-colors">
                    Add Purchas
                </button> --}}
            </div>

            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <div class="relative overflow-x-auto shadow-md rounded-lg">

                    <div class="p-4 md:p-5 space-y-4">
                        {{-- First Line --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Name: {{ $selectedVendor }}</label>
                                <select wire:model="selectedVendor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                    <option value="">Select Vendor </option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->id }}-{{ $vendor->name_entpr }}</option>
                                    @endforeach
                                    
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Invoice No: {{ $invoiceNo }}</label>
                                <input wire:model="invoiceNo" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Date: {{ $invoiceDate }}</label>
                                <input wire:model="invoiceDate" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            </div>
                        </div>
                        {{-- Second Line     --}}

                        <!-- Product Details Section -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-medium text-gray-800">Product Details</h4>
                                <button 
                                    {{-- wire:click="openProductModal"  --}}
                                    type="button" onclick="addProductRow()" class="px-3 py-1 text-sm font-medium text-white bg-blue-500 rounded hover:bg-blue-600 transition-colors">
                                    Add Product
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-700 border border-gray-300">
                                    <thead class="text-gray-600 uppercase bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-3 border-b border-gray-300">Category</th>
                                            <th class="px-4 py-3 border-b border-gray-300">Name</th>
                                            <th class="px-4 py-3 border-b border-gray-300">Unit</th>
                                            <th class="px-4 py-3 border-b border-gray-300">Quantity</th>
                                            <th class="px-4 py-3 border-b border-gray-300">Rate</th>
                                            <th class="px-4 py-3 border-b border-gray-300">Amount</th>
                                            <th class="px-4 py-3 border-b border-gray-300">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productTableBody">
                                        
                                    </tbody>
                                </table>
                            </div>

                        </div>   

                        <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button data-modal-hide="default-modal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Cancel</button>
                            <button wire:click="" data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Accept</button>
                        </div>


                    </div>
                </div>
            </div>


        </div>
    </div>

        
    <!-- Main modal -->
    <div id="default-modal" tabindex="-1" aria-hidden="true" class="{{ $showProductModal ? 'block' : 'hidden' }} fixed inset-0 z-50 flex justify-center items-center w-full h-full bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-6xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Purchase Product Entry
                    </h3>
                    <button wire:click="closeProductModal" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                
                <div class="p-4 md:p-5 space-y-4">
                    {{-- First Line --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category: {{ $selectedCategory }}</label>
                            <select wire:model="selectedCategory" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->id }}-{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Item Name: {{ $selectedItem }}</label>
                            <select wire:model="selectedItem" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>

                                <option value="">Select Item</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->id }}-{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Unit: {{ $selectedPurchaseUnit }}</label>
                            <select wire:model="selectedPurchaseUnit" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                <option value="">Select Purchase Unit</option>
                                @foreach($purchaseUnits as $purchaseUnit)
                                    <option value="{{ $purchaseUnit->id }}">{{ $purchaseUnit->id }}-{{ $purchaseUnit->name }}</option>
                                @endforeach
                                
                            </select>
                        </div>

                    </div>
                    {{-- Second Line --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Rate: {{ $purchaseRate }} </label>
                            <input wire:model="purchaseRate" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Quantity: {{ $purchaseQty }}</label>
                            <input wire:model="purchaseQty" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>                       

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount: {{ $purchaseAmount }} </label>
                            <input wire:model="purchaseAmount" disabled="true" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>
                    </div>

                    {{-- Third Line --}}
                    {{-- <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                        <div class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="true" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 dark:peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Toggle me</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Unit</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>                       

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>
                    </div> --}}


                    
                    

                </div>
                <!-- Modal footer -->
                <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button wire:click="closeProductModal" data-modal-hide="default-modal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I accept</button>
                    <button wire:click="saveProductData" data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Decline</button>
                </div>
            </div>
        </div>
    </div>


     <script>
        let rowCounter = 0;

        function addProductRow() {
            rowCounter++;
            const tbody = document.getElementById('productTableBody');
            
            const row = document.createElement('tr');
            row.className = 'border-b border-gray-200 hover:bg-gray-50';
            row.id = `productRow_${rowCounter}`;
            
            // Build the row HTML with proper escaping
            const categoryOptions = `
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            `;
            
            const unitOptions = `
                <option value="">Select Unit</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            `;
            
            row.innerHTML = `
                <td class="px-4 py-3">
                    <select wire:model="productDetails.` + rowCounter + `.category_id" wire:change="updateProductOptions(` + rowCounter + `)" class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                        ` + categoryOptions + `
                    </select>
                </td>
                <td class="px-4 py-3">
                    <select wire:model="productDetails.` + rowCounter + `.product_id" class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                        <option value="">Select Product</option>
                    </select>
                </td>
                <td class="px-4 py-3">
                    <select wire:model="productDetails.` + rowCounter + `.unit_id" class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                        ` + unitOptions + `
                    </select>
                </td>
                <td class="px-4 py-3">
                    <input wire:model="productDetails.` + rowCounter + `.quantity" wire:change="calculateAmount(` + rowCounter + `)" type="number" class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500" min="0" step="0.01" required>
                </td>
                <td class="px-4 py-3">
                    <input wire:model="productDetails.` + rowCounter + `.rate" wire:change="calculateAmount(` + rowCounter + `)" type="number" class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500" min="0" step="0.01" required>
                </td>
                <td class="px-4 py-3">
                    <input wire:model="productDetails.` + rowCounter + `.amount" type="number" class="w-full px-2 py-1 border border-gray-300 rounded text-xs bg-gray-100" readonly>
                </td>
                <td class="px-4 py-3">
                    <button type="button" wire:click="removeProductRow(` + rowCounter + `)" class="px-2 py-1 text-xs font-medium text-white bg-red-500 rounded hover:bg-red-600 transition-colors">
                        Remove
                    </button>
                </td>
            `;
            
            tbody.appendChild(row);
        }
        
        function removeProductRow(rowId) {
            const row = document.getElementById(`productRow_${rowId}`);
            if (row) {
                row.remove();
                updateTotalAmount();
            }
        }
        
        // Add first row by default
        document.addEventListener('DOMContentLoaded', function() {
            addProductRow();
        });
    </script>

</div>
