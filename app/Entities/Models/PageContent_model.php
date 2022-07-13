<?php

namespace BookStack\Entities\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageContent_model extends Model
{
    use HasFactory;
    protected $fillable = ['page_id','page_sub_title','page_description'];
    // protected $casts = [
    //     'page_sub_title' => 'array',
    //     'page_description' => 'array',
    //   ];
}
