<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    // Table name (optional if following Laravel naming conventions)
    protected $table = 'teachers';

    // Primary key
    protected $primaryKey = 'teacher_id';

    // Primary key is not auto-incrementing
    public $incrementing = false;

    // Primary key type
    protected $keyType = 'string';

    // Mass assignable fields
    protected $fillable = [
        'teacher_id',
        'teacher_name',
        'class_id',
        'subject_id',
    ];

    // Relationships

    public function classes()
{
    // return $this->belongsToMany(Classes::class, 'class_teacher', 'teacher_id', 'class_id');
    return $this->belongsToMany(Classes::class,'class_teacher','teacher_id','class_id');
}


    // public function teacher(){

    // }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }
}
