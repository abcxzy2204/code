@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <div class="mx-auto px-4 py-8" style="width: 500px;">
        <!-- Header Section -->
        <div class="text-center mb-10 border-4 border-blue-600 rounded-lg pt-6">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Khám Phá Danh Mục
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                Tìm kiếm và khám phá các danh mục sản phẩm đa dạng của chúng tôi
            </p>
        </div>

        <!-- Search Section -->
        <div class="mb-12 flex justify-center">
            <div class="relative w-full max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       placeholder="Tìm kiếm danh mục..." 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-lg transition-all duration-300 hover:shadow-xl bg-white/80 backdrop-blur-sm" />
            </div>
        </div>

        @if($categories->isEmpty())
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Chưa có danh mục nào</h3>
                <p class="text-gray-500">Hãy quay lại sau để khám phá các danh mục mới</p>
            </div>
        @else
            <!-- Categories Content -->
            <div class="space-y-12">
                <!-- Parent Categories Section -->
                <div x-data="{ open: true }" class="bg-white rounded-2xl shadow-xl border border-white/20 p-8">
                    <div class="mb-4 flex items-center cursor-pointer select-none" @click="open = !open">
                        <div class="flex-grow text-center">
                            <h2 class="text-3xl font-bold text-gray-800 mb-2 relative inline-block">
                                Danh Mục Chính
                                <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-16 h-1 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full"></div>
                            </h2>
                        </div>
                        <button type="button" class="text-blue-600 focus:outline-none" aria-label="Toggle Danh Mục Chính">
                            <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transform rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transform transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                    
                    <div x-show="open" x-transition class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($categories as $parent)
                            <a href="{{ route('user.home', ['category' => $parent->id]) }}" 
                               class="group relative overflow-hidden bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-xl p-6 text-center transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 hover:border-blue-300">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <div class="relative z-10">
                                    <div class="w-12 h-12 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center text-white font-bold text-lg">
                                        {{ strtoupper(substr($parent->name, 0, 2)) }}
                                    </div>
                                    <h3 class="font-semibold text-gray-800 group-hover:text-blue-600 transition-colors duration-300">
                                        {{ $parent->name }}
                                    </h3>
                                    <div class="mt-2 h-0.5 w-0 bg-gradient-to-r from-blue-500 to-purple-500 mx-auto group-hover:w-full transition-all duration-300"></div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Child Categories Section -->
                <div x-data="{ open: true }" class="bg-white rounded-2xl shadow-xl border border-white/20 p-8 mt-8">
                    <div class="mb-4 flex items-center cursor-pointer select-none" @click="open = !open">
                        <div class="flex-grow text-center">
                            <h2 class="text-3xl font-bold text-gray-800 mb-2 relative inline-block">
                                Danh Mục Chi Tiết
                                <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-16 h-1 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full"></div>
                            </h2>
                        </div>
                        <button type="button" class="text-purple-600 focus:outline-none" aria-label="Toggle Danh Mục Chi Tiết">
                            <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transform rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transform transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                    
                    <div x-show="open" x-transition class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach($categories as $parent)
                            @foreach($parent->children as $child)
                                <a href="{{ route('user.home', ['category' => $child->id]) }}" 
                                   class="group relative overflow-hidden bg-gradient-to-br from-white to-purple-50 border border-purple-200 rounded-lg p-4 text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:border-purple-400 hover:bg-gradient-to-br hover:from-purple-50 hover:to-pink-50">
                                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-pink-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    <div class="relative z-10">
                                        <div class="w-8 h-8 mx-auto mb-2 bg-gradient-to-br from-purple-400 to-pink-400 rounded-md flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($child->name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 group-hover:text-purple-600 transition-colors duration-300 line-clamp-2">
                                            {{ $child->name }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
