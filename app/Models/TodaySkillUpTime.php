<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodaySkillUpTime extends Model
{
    // use HasFactory;

    protected $table = 'today_skill_up_time_management';

    protected $fillable = [
        'user_name',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'total_study_time',
        'study_content',
        'start_flag',
        'break_flag',
        'end_flag',
    ];
}