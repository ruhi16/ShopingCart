<div>
    <div class="bg-gray-100 flex items-center justify-center ">
    <div class="container mx-auto p-4">
        <div class="flex justify-around items-center mb-4 p-2 bg-red-300 rounded-lg shadow-lg">
            <h2 class="text-lg font-semibold text-gray-800">Category Data Table</h2>
            <button onclick="handleAdd()" class="px-3 py-1.5 text-sm font-semibold text-white bg-green-500 rounded hover:bg-green-600 transition-colors">
                Add Category
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
                    @foreach($categories as $category)
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-2 border-b border-gray-200">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2 border-b border-gray-200">{{ $category->name }}</td>
                        <td class="px-4 py-2 border-b border-gray-200"></td>
                        <td class="px-4 py-2 border-b border-gray-200"></td>
                        <td class="px-4 py-2 border-b border-gray-200">
                            <span class="inline-flex px-2 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </td>
                        <td class="px-4 py-2 border-b border-gray-200">
                            <button onclick="handleAction(1)" class="px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded hover:bg-blue-600 transition-colors">Edit</button>
                        </td>
                    </tr>
                    @endforeach
                    {{-- <tr class="bg-gray-50 hover:bg-gray-100 transition-colors">
                        <td class="px-4 py-2 border-b border-gray-200">2</td>
                        <td class="px-4 py-2 border-b border-gray-200">Jane Smith</td>
                        <td class="px-4 py-2 border-b border-gray-200">jane.smith@example.com</td>
                        <td class="px-4 py-2 border-b border-gray-200">User</td>
                        <td class="px-4 py-2 border-b border-gray-200">
                            <span class="inline-flex px-2 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        </td>
                        <td class="px-4 py-2 border-b border-gray-200">
                            <button onclick="handleAction(2)" class="px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded hover:bg-blue-600 transition-colors">Edit</button>
                        </td>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-2 border-b border-gray-200">3</td>
                        <td class="px-4 py-2 border-b border-gray-200">Alice Johnson</td>
                        <td class="px-4 py-2 border-b border-gray-200">alice.j@example.com</td>
                        <td class="px-4 py-2 border-b border-gray-200">Editor</td>
                        <td class="px-4 py-2 border-b border-gray-200">
                            <span class="inline-flex px-2 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </td>
                        <td class="px-4 py-2 border-b border-gray-200">
                            <button onclick="handleAction(3)" class="px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded hover:bg-blue-600 transition-colors">Edit</button>
                        </td>
                    </tr>
                    <tr class="bg-gray-50 hover:bg-gray-100 transition-colors">
                        <td class="px-4 py-2 border-b border-gray-200">4</td>
                        <td class="px-4 py-2 border-b border-gray-200">Bob Brown</td>
                        <td class="px-4 py-2 border-b border-gray-200">bob.brown@example.com</td>
                        <td class="px-4 py-2 border-b border-gray-200">User</td>
                        <td class="px-4 py-2 border-b border-gray-200">
                            <span class="inline-flex px-2 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                        </td>
                        <td class="px-4 py-2 border-b border-gray-200">
                            <button onclick="handleAction(4)" class="px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded hover:bg-blue-600 transition-colors">Edit</button>
                        </td>
                    </tr> --}}
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