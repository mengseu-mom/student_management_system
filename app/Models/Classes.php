<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $primaryKey = 'class_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'class_id',
        'class_name',
        'status',
        'user_id', 
    ];

    protected static function booted()
    {
        static::creating(function ($class) {
            if (!$class->class_id) {
                $maxId = DB::table('classes')
                    ->select(DB::raw('MAX(CAST(SUBSTRING(class_id, 2) AS UNSIGNED)) as max_id'))
                    ->value('max_id');
                $class->class_id = $maxId ? 'C' . str_pad($maxId + 1, 3, '0', STR_PAD_LEFT) : 'C001';
            }
        });
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function students()
    {
        return $this->hasMany(StudentList::class, 'class_id', 'class_id');
    }
}
