@extends('landlord.layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-10 max-w-7xl">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Danh Sách Danh Mục</h2>
            <a href="{{ route('staff.categories.create') }}"
                class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Thêm Danh Mục
            </a>
        </div>

        <!-- Categories Card -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider w-16">
                                ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Tên
                            </th>
                            <th
                                class="px-6 py-4 text-right text-sm font-semibold text-gray-600 uppercase tracking-wider w-56">
                                Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($categories as $cat)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 text-gray-700 font-medium">{{ $cat->category_id }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $cat->name }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex space-x-2">
                                        <a href="{{ route('staff.categories.edit', $cat->category_id) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 focus:ring-2 focus:ring-yellow-200 transition-all duration-300">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Sửa
                                        </a>
                                        <form method="POST"
                                            action="{{ route('staff.categories.destroy', $cat->category_id) }}"
                                            class="inline-flex"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 focus:ring-2 focus:ring-red-200 transition-all duration-300">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a1 1 0 011 1v1H9V4a1 1 0 011-1z">
                                                    </path>
                                                </svg>
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-lg">
                                    Không có danh mục nào được tìm thấy
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if ($categories->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $categories->links('vendor.pagination.custom-tailwind') }}
            </div>
        @endif
    </div>

    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Include Toastify.js CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <!-- Toast Notification Script -->
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Toastify({
                    text: "{{ session('success') }}",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#10b981",
                    stopOnFocus: true,
                }).showToast();
            });
        </script>
    @endif
@endsection
