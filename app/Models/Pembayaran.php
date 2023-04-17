<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable= [
        'user_id',
        'siswa_id',
        'spp_id',
        'tanggal_bayar',
        'jumlah_bayar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function spp()
    {
        return $this->belongsTo(SPP::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
