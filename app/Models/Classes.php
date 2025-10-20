<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

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
        'teacher_name'
    ];

    // Relationship: One class has many students
    public function students(){
        return $this->hasMany(StudentList::class, 'class_id');
    }

}
