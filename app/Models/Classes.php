<?php

namespace App\Models;

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
        'user_id', // foreign key to users table (teacher)
    ];

    /**
     * Relationship: A class belongs to one teacher (user)
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: One class has many students
     */
    public function students()
    {
        return $this->hasMany(StudentList::class, 'class_id', 'class_id');
    }
}
