<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <div class="bg-gray-100 flex items-center justify-center ">
        <div class="container mx-auto p-4">
            <div class="flex justify-around items-center mb-4 p-2 bg-red-300 rounded-lg shadow-lg">
                <h2 class="text-lg font-semibold text-gray-800">Purchase Data Table</h2>
                <button onclick="handleAdd()" class="px-3 py-1.5 text-sm font-semibold text-white bg-green-500 rounded hover:bg-green-600 transition-colors">
                    Add Purchas
                </button>
            </div>
            <div class="relative overflow-x-auto shadow-md rounded-lg">
                <table class="w-full text-xs text-left text-gray-700 border-separate border-spacing-y-0">
                    <thead class="text-gray-600 uppercase bg-gray-200">
                        <tr>
                            <th class="px-4 py-2.5 border-b border-gray-300">ID</th>
                            <th class="px-4 py-2.5 border-b border-gray-300">Vendor Name</th>
                            <th class="px-4 py-2.5 border-b border-gray-300">Product Details</th>
                            <th class="px-4 py-2.5 border-b border-gray-300">Role</th>
                            <th class="px-4 py-2.5 border-b border-gray-300">Status</th>
                            <th class="px-4 py-2.5 border-b border-gray-300">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchases as $purchase)
                        <tr class="bg-white hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-2 border-b border-gray-200">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 border-b border-gray-200">{{ $purchase->vendor_id }}</td>
                            <td class="px-4 py-2 border-b border-gray-200">

                                <table class="w-full text-xs text-left text-gray-700 border-separate border-spacing-y-0">
                                    <thead class="text-gray-600 uppercase bg-gray-200">
                                        <th class="px-4 py-1.5 border-b border-gray-300">ID</th>
                                        <th class="px-4 py-1.5 border-b border-gray-300">Product Name</th>
                                        <th class="px-4 py-1.5 border-b border-gray-300">Unit</th>
                                        <th class="px-4 py-1.5 border-b border-gray-300">Qty</th>
                                        <th class="px-4 py-1.5 border-b border-gray-300">Rate</th>
                                        <th class="px-4 py-1.5 border-b border-gray-300">Amount</th>
                                    </thead>
                                    <tbody>
                                        @foreach($purchase->purchaseDetails as $purchaseDetail)
                                        <tr class="bg-white hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-1 border-b border-gray-200">{{ $loop->iteration }}</td>
                                            <td class="px-4 py-1 border-b border-gray-200">
                                                {{ $purchaseDetail->product->Category->name ?? 'x'}}-{{ $purchaseDetail->product->Item->name ?? 'x'}}

                                            </td>
                                            <td class="px-4 py-1 border-b border-gray-200">
                                                {{ $purchaseDetail->purchaseUnit->name ?? 'x'}}
                                            </td>
                                            <td class="px-4 py-1 border-b border-gray-200">
                                                {{ $purchaseDetail->purchase_unit_qty ?? 'x'}}
                                            </td>
                                            <td class="px-4 py-1 border-b border-gray-200">
                                                {{ $purchaseDetail->purchase_unit_rate ?? 'x'}}
                                            </td>
                                            <td class="px-4 py-1 border-b border-gray-200 text-right">
                                                {{ $purchaseDetail->purchase_amount ?? 'x'}}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr class="bg-white hover:bg-gray-50 transition-colors text-md uppercase">
                                            <td colspan="2" class="px-4 py-1 border-b border-gray-200 font-bold text-right"></td>
                                            <td colspan="3" class="px-4 py-1 border-b border-gray-200 font-bold text-left">
                                                Total Amount:
                                            </td>
                                            <td class="px-4 py-1 border-b border-gray-200 font-bold text-right">
                                                {{ $purchase->purchaseDetails->sum('purchase_amount') ?? 'x'}}
                                            </td>
                                        </tr>
                                        <tr class="bg-white hover:bg-gray-50 transition-colors text-md uppercase">
                                            <td colspan="2" class="px-4 py-1 border-b border-gray-200 font-bold text-right"></td>
                                            <td colspan="3" class="px-4 py-1 border-b border-gray-200 font-bold text-left">
                                                Total Discount(if any):
                                            </td>
                                            <td class="px-4 py-1 border-b border-gray-200 font-bold text-right">
                                                0
                                            </td>
                                        </tr>
                                        <tr class="bg-white hover:bg-gray-50 transition-colors text-md uppercase">
                                            <td colspan="2" class="px-4 py-1 border-b border-gray-200 font-bold text-right"></td>
                                            <td colspan="3" class="px-4 py-1 border-b border-gray-200 font-bold text-left">
                                                Total Payable:
                                            </td>
                                            <td class="px-4 py-1 border-b border-gray-200 font-bold text-right">
                                                {{ $purchase->purchaseDetails->sum('purchase_amount') ?? 'x'}}
                                            </td>
                                        </tr>




                                    </tbody>
                                </table>


                            </td>
                            <td class="px-4 py-2 border-b border-gray-200"></td>
                            <td class="px-4 py-2 border-b border-gray-200">
                                <span class="inline-flex px-2 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            </td>
                            <td class="px-4 py-2 border-b border-gray-200">
                                <button onclick="handleAction(1)" class="px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded hover:bg-blue-600 transition-colors">Edit</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


<!-- Modal toggle -->
{{-- <button 
    wire:click="openModal" 
    onclick="openModal()"
    data-modal-target="default-modal" data-modal-toggle="default-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
    Toggle modal
</button> --}}

<!-- Main modal -->
<div id="default-modal" tabindex="-1" aria-hidden="true" class="{{ $showModal ? 'block' : 'hidden' }} fixed inset-0 z-50 flex justify-center items-center w-full h-full bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="relative p-4 w-full max-w-6xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Terms of Service
                </h3>
                <button wire:click="closeModal" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Name</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            <option value="">Select Vendor</option>
                            <option value="vendor1">Vendor 1</option>
                            <option value="vendor2">Vendor 2</option>
                            <option value="vendor3">Vendor 3</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Invoice No</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Date</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    </div>
                </div>
                {{-- Second Line     --}}

                <!-- Product Details Section -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-medium text-gray-800">Product Details</h4>
                            <button type="button" onclick="addProductRow()" class="px-3 py-1 text-sm font-medium text-white bg-blue-500 rounded hover:bg-blue-600 transition-colors">
                                Add Product
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-700 border border-gray-300">
                                <thead class="text-gray-600 uppercase bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border-b border-gray-300">Category</th>
                                        <th class="px-4 py-2 border-b border-gray-300">Name</th>
                                        <th class="px-4 py-2 border-b border-gray-300">Unit</th>
                                        <th class="px-4 py-2 border-b border-gray-300">Quantity</th>
                                        <th class="px-4 py-2 border-b border-gray-300">Rate</th>
                                        <th class="px-4 py-2 border-b border-gray-300">Amount</th>
                                        <th class="px-4 py-2 border-b border-gray-300">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                {{-- <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                    With less than a month to go before the European Union enacts new consumer privacy laws for its citizens, companies around the world are updating their terms of service agreements to comply.
                </p>
                <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                    The European Union’s General Data Protection Regulation (G.D.P.R.) goes into effect on May 25 and is meant to ensure a common set of data rights in the European Union. It requires organizations to notify users as soon as possible of high-risk data breaches that could personally affect them.
                </p> --}}

            </div>
            <!-- Modal footer -->
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button data-modal-hide="default-modal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Cancel</button>
                <button wire:click="closeModal" data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Accept</button>
            </div>
            
        </div>
    </div>
</div>
































    <script>
        
        // Modal functions
        function openModal() {
            //@this.call('openModal')
            //document.getElementById('default-modal').classList.remove('hidden');
            //document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            //@this.call('closeModal')
            //document.getElementById('default-modal').classList.add('hidden');
            //document.body.style.overflow = 'auto';
            //resetForm();
        }
    </script>

    <script>
        function handleAction(id) {
            alert(`Edit action triggered for ID: ${id}`);
            // Replace alert with actual action, e.g., open edit form, API call, etc.
        }

        function handleAdd() {
            alert(`Add new user action triggered`);
            // Replace alert with actual action, e.g., open add user form, API call, etc.
        }

    </script>
</div>
