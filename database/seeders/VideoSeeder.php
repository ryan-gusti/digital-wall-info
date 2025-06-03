<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $videos = [
            [
                'title' => 'Video Promosi Perusahaan',
                'description' => 'Video promosi mengenai profil dan layanan perusahaan',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                'thumbnail_url' => 'https://peach.blender.org/wp-content/uploads/title_anouncement.jpg?x11217',
                'duration' => 596,
                'video_type' => 'mp4',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'title' => 'Informasi Produk Terbaru',
                'description' => 'Pengenalan produk dan fitur terbaru',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
                'thumbnail_url' => 'https://download.blender.org/ED/ED_poster.jpg',
                'duration' => 653,
                'video_type' => 'mp4',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'title' => 'Tutorial Penggunaan Sistem',
                'description' => 'Panduan lengkap menggunakan sistem digital wall',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
                'thumbnail_url' => 'https://storage.googleapis.com/gtv-videos-bucket/sample/images/ForBiggerBlazes.jpg',
                'duration' => 15,
                'video_type' => 'mp4',
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'title' => 'Berita dan Update Terkini',
                'description' => 'Informasi berita dan update terbaru dari perusahaan',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
                'thumbnail_url' => 'https://storage.googleapis.com/gtv-videos-bucket/sample/images/ForBiggerEscapes.jpg',
                'duration' => 15,
                'video_type' => 'mp4',
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'title' => 'Testimoni Pelanggan',
                'description' => 'Testimoni dan review dari pelanggan setia',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4',
                'thumbnail_url' => 'https://storage.googleapis.com/gtv-videos-bucket/sample/images/ForBiggerFun.jpg',
                'duration' => 60,
                'video_type' => 'mp4',
                'is_active' => true,
                'sort_order' => 5
            ]
        ];

        foreach ($videos as $video) {
            \App\Models\Video::create($video);
        }
    }
}
