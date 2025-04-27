<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TodaySkillUpTimeRecord extends Component
{
  public $todaySkillUpTimeAllRecords;

  public function __construct($todaySkillUpTimeAllRecords)
  {
    $this->todaySkillUpTimeAllRecords = $todaySkillUpTimeAllRecords;
  }

  public function render()
  {
    return view('components.today-skill-up-time-record');
  }
}