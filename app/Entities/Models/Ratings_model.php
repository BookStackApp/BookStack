<?php

namespace BookStack\Entities\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratings_model extends model
{
    use HasFactory;
    protected $fillable = ['user_id','experience_rating','empathetic_rating','doctor_attends_rating','satisfied_doctor_rating','additional_comments'];
}
