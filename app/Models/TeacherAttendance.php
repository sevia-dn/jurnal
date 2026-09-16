<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    use HasFactory;

    // Nama tabel di database (sesuaikan jika nama tabel Anda berbeda, misal: 'teacher_attendances')
    protected $table = 'teacher_attendances';

    protected $fillable = [
        'user_id',
        'date',
        'status',
        'time',
        'keterangan',
    ];

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}