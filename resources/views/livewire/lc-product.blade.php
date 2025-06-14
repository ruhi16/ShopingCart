<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    <div class="bg-gray-100 flex items-center justify-center ">
    <div class="container mx-auto p-4">
        <div class="flex justify-around items-center mb-4 p-2 bg-red-300 rounded-lg shadow-lg">
            <h2 class="text-lg font-semibold text-gray-800">Products Data Table</h2>
            <button onclick="handleAdd()" class="px-3 py-1.5 text-sm font-semibold text-white bg-green-500 rounded hover:bg-green-600 transition-colors">
                Add Product
            </button>
        </div>
        <div class="relative overflow-x-auto shadow-md rounded-lg">
            <table class="w-full text-xs text-left text-gray-700 border-separate border-spacing-y-0">
                <thead class="text-gray-600 uppercase bg-gray-200">
                    <tr>
                        <th class="px-4 py-2.5 border-b border-gray-300">ID</th>
                        <th class="px-4 py-2.5 border-b border-gray-300">Name</th>
                        <th class="px-4 py-2.5 border-b border-gray-300">Email</th>
                        <th class="px-4 py-2.5 border-b border-gray-300">Role</th>
                        <th class="px-4 py-2.5 border-b border-gray-300">Status</th>
                        <th class="px-4 py-2.5 border-b border-gray-300">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-2 border-b border-gray-200">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2 border-b border-gray-200">{{ $product->category->name }}</td>
                        <td class="px-4 py-2 border-b border-gray-200">{{ $product->item->name }}</td>
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
