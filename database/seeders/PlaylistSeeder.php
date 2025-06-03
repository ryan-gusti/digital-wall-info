<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlaylistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat playlist utama
        $playlist1 = \App\Models\Playlist::create([
            'name' => 'Playlist Utama Kantor',
            'description' => 'Playlist utama untuk ditampilkan di digital wall kantor',
            'is_active' => true,
            'auto_play' => true,
            'loop_playlist' => true,
            'sort_order' => 1
        ]);

        $playlist2 = \App\Models\Playlist::create([
            'name' => 'Informasi Produk',
            'description' => 'Playlist khusus untuk informasi produk dan layanan',
            'is_active' => true,
            'auto_play' => true,
            'loop_playlist' => false,
            'sort_order' => 2
        ]);

        $playlist3 = \App\Models\Playlist::create([
            'name' => 'Training & Tutorial',
            'description' => 'Playlist untuk video training dan tutorial',
            'is_active' => false,
            'auto_play' => true,
            'loop_playlist' => true,
            'sort_order' => 3
        ]);

        // Menambahkan video ke playlist
        $videos = \App\Models\Video::all();

        if ($videos->count() >= 3) {
            // Playlist 1: Video 1, 2, 5
            $playlist1->videos()->attach([
                $videos[0]->id => ['sort_order' => 1],
                $videos[1]->id => ['sort_order' => 2],
                $videos[4]->id => ['sort_order' => 3]
            ]);

            // Playlist 2: Video 1, 2, 3
            $playlist2->videos()->attach([
                $videos[0]->id => ['sort_order' => 1],
                $videos[1]->id => ['sort_order' => 2],
                $videos[2]->id => ['sort_order' => 3]
            ]);

            // Playlist 3: Video 3, 4
            $playlist3->videos()->attach([
                $videos[2]->id => ['sort_order' => 1],
                $videos[3]->id => ['sort_order' => 2]
            ]);
        }
    }
}
