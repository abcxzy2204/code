<?php

namespace App\Exports;

use App\Models\Post;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PostsExport implements FromCollection, WithHeadings
{
    /**
     * Return a collection of posts to be exported.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Post::select('id', 'title', 'short_description', 'content', 'created_at', 'updated_at')->get();
    }

    /**
     * Define the headings for the Excel sheet.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Short Description',
            'Content',
            'Created At',
            'Updated At',
        ];
    }
}
