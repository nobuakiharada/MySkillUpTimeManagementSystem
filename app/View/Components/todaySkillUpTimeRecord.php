<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TodaySkillUpTimeRecord extends Component
{
    public $todaySkillUpTimeRecord;

    public function __construct($todaySkillUpTimeRecord)
    {
        $this->todaySkillUpTimeRecord = $todaySkillUpTimeRecord;
    }

    public function render()
    {
        return view('components.today-skill-up-time-record');
    }
}