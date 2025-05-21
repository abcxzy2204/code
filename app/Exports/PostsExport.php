<?php

namespace App\Exports;

use App\Models\Post;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PostsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Post::select('title', 'short_description', 'created_at')->get();
    }

    public function headings(): array
    {
        return [
            'Tiêu đề',
            'Mô tả ngắn',
            'Ngày tạo',
        ];
    }
}
