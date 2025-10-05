<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Reward extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'points_required',
        'image',
        // 'status' // Jika Anda memutuskan untuk menambahkannya nanti
    ];

    /**
     * [FUNGSI BARU] Mendapatkan URL gambar reward.
     * Jika tidak ada gambar, kembalikan placeholder.
     */
    public function getImageUrl()
    {
        // Asumsi nama kolom gambar di database adalah 'image'
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::url($this->image);
        }

        // Jika tidak ada gambar, tampilkan placeholder
        return 'https://placehold.co/600x400/E2E8F0/4A5568?text=No+Image';
    }
}
