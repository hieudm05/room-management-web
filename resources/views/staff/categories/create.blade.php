@extends('landlord.layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-10 max-w-7xl">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 animate__animated animate__fadeIn">
            <h2 class="text-3xl font-extrabold text-indigo-600 tracking-tight">
                <i class="bi bi-folder-plus me-2"></i>Thêm Danh Mục Mới
            </h2>
            <a href="{{ route('staff.categories.index') }}"
                class="inline-flex items-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:ring-4 focus:ring-gray-300 transition-all duration-300 shadow-sm">
                <i class="bi bi-arrow-left me-2"></i>Quay lại danh sách
            </a>
        </div>

        <!-- Category Form -->
        <div class="bg-white rounded-xl shadow-md p-6 animate__animated animate__fadeInUp">
            <form method="POST" action="{{ route('staff.categories.store') }}">
                @csrf
                <!-- Tên danh mục -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tên danh mục <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 transition-all"
                        placeholder="Nhập tên danh mục...">
                    @error('name')
                        <small class="text-red-500 mt-1 block">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Slug (auto-generated) -->
                {{-- <div class="mb-6">
                    <label for="slug" class="block text-sm font-semibold text-gray-700 mb-2">
                        Slug
                    </label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed"
                        placeholder="Slug tự động..." readonly>
                    @error('slug')
                        <small class="text-red-500 mt-1 block">{{ $message }}</small>
                    @enderror
                </div> --}}

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 shadow-md">
                    <i class="bi bi-check-circle me-2"></i>Lưu danh mục
                </button>
            </form>
        </div>
    </div>

    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Include Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- Include Toastify.js CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <!-- Toast Notification and Slug Generation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Success Message Toast
            @if (session('success'))
                Toastify({
                    text: "{{ session('success') }}",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#10b981", // Tailwind green-500
                    stopOnFocus: true,
                    className: "rounded-lg shadow-lg",
                }).showToast();
            @endif

            // Error Messages Toast
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    Toastify({
                        text: "{{ $error }}",
                        duration: 4000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#ef4444", // Tailwind red-500
                        stopOnFocus: true,
                        className: "rounded-lg shadow-lg",
                    }).showToast();
                @endforeach
            @endif

            // Slug Auto-Generation
            const nameInput = document.querySelector('input[name="name"]');
            const slugInput = document.querySelector('input[name="slug"]');
            if (nameInput && slugInput) {
                nameInput.addEventListener('input', function() {
                    slugInput.value = nameInput.value
                        .toLowerCase()
                        .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // Remove Vietnamese diacritics
                        .replace(/[^a-z0-9\s-]/g, "") // Remove special characters
                        .trim()
                        .replace(/\s+/g, "-"); // Replace spaces with hyphens
                });
            }
        });
    </script>
@endsection
```
