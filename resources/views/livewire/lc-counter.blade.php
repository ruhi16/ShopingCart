<div>
    {{-- {{ $count }} --}}
    {{-- <button wire:click="inc">Inc</button> --}}
    {{-- <button wire:click="dec">Dec</button> --}}

    <div class="bg-gray-100 flex items-center justify-center ">
        <div class="container mx-auto p-4">
            <div class="relative overflow-x-auto shadow-md rounded-lg">
                <table class="w-full text-xs text-left text-gray-700 border-separate border-spacing-y-0">
                    <thead class="text-gray-600 uppercase bg-gray-200">
                        <tr>
                            <th class="px-4 py-2.5 border-b border-gray-300">ID</th>
                            <th class="px-4 py-2.5 border-b border-gray-300">Name</th>
                            <th class="px-4 py-2.5 border-b border-gray-300">Email</th>
                            <th class="px-4 py-2.5 border-b border-gray-300">Role</th>
                            <th class="px-4 py-2.5 border-b border-gray-300">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-2 border-b border-gray-200">1</td>
                            <td class="px-4 py-2 border-b border-gray-200">John Doe</td>
                            <td class="px-4 py-2 border-b border-gray-200">john.doe@example.com</td>
                            <td class="px-4 py-2 border-b border-gray-200">Admin</td>
                            <td class="px-4 py-2 border-b border-gray-200">
                                <span class="inline-flex px-2 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            </td>
                        </tr>
                        <tr class="bg-gray-50 hover:bg-gray-100 transition-colors">
                            <td class="px-4 py-2 border-b border-gray-200">2</td>
                            <td class="px-4 py-2 border-b border-gray-200">Jane Smith</td>
                            <td class="px-4 py-2 border-b border-gray-200">jane.smith@example.com</td>
                            <td class="px-4 py-2 border-b border-gray-200">User</td>
                            <td class="px-4 py-2 border-b border-gray-200">
                                <span class="inline-flex px-2 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
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
                        </tr>
                        <tr class="bg-gray-50 hover:bg-gray-100 transition-colors">
                            <td class="px-4 py-2 border-b border-gray-200">4</td>
                            <td class="px-4 py-2 border-b border-gray-200">Bob Brown</td>
                            <td class="px-4 py-2 border-b border-gray-200">bob.brown@example.com</td>
                            <td class="px-4 py-2 border-b border-gray-200">User</td>
                            <td class="px-4 py-2 border-b border-gray-200">
                                <span class="inline-flex px-2 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>
