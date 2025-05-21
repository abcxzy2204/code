<x-app-layout>
<div class="p-6 rounded-lg shadow-md space-y-6" style="background: linear-gradient(to right, #a78bfa, #f9a8d4, #fcd34d);">

@if(session('success'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Thành công!',
        text: '{{ session('success') }}',
        confirmButtonText: 'OK'
    });
</script>
@endif


<div class="flex justify-around text-center space-x-6 space-y-0 flex-wrap">
        <div style="margin: 0 5px" class="bg-white rounded-lg p-4 shadow flex-1 flex items-center space-x-4 justify-center min-w-[200px]">
            
            <div>
                <p class="text-gray-600 font-semibold">Tổng số bài viết</p>
                <p class="text-2xl font-bold">{{ $totalPosts }}</p>
            </div>
        </div>

        <div style="margin: 0 5px" class="bg-white rounded-lg p-4 shadow flex-1 flex items-center space-x-4 justify-center min-w-[200px]">
         
            <div>
                <p class="text-gray-600 font-semibold">Bài viết mới nhất</p>
                <p class="text-2xl font-bold">{{ $latestPostCount }}</p>
            </div>
        </div>

        <div style="margin: 0 5px" class="bg-white rounded-lg p-4 shadow flex-1 flex items-center space-x-4 justify-center min-w-[200px]">
           
            <div>
                <p class="text-gray-600 font-semibold">Ngày tạo mới nhất</p>
                <p class="text-2xl font-bold">{{ $latestPostDate }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 max-w-6xl mx-auto">
        <h2 class="text-lg font-semibold mb-4 text-center">Chức năng quản lý</h2>
        <div class="flex justify-center space-x-4">
            <a href="{{ route('posts.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-black border border-gray-300 rounded hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm bài viết
            </a>
            <a href="{{ route('posts.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-black border border-gray-300 rounded hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Quản lý bài viết
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 max-w-6xl mx-auto">
        <h2 class="text-lg font-semibold mb-4 text-center">Biểu đồ số lượng bài viết trong 7 ngày gần nhất</h2>
        <canvas id="postsChart" width="900" height="300"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('postsChart').getContext('2d');
        const postsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($dates),
                datasets: [{
                    label: 'Số lượng bài viết',
                    data: @json($counts),
                    backgroundColor: 'rgba(99, 102, 241, 0.2)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Ngày'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số lượng bài viết'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
