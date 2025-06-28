<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    protected $table = 'survey_responses';
    protected $fillable = ['event_id', 'venue_rating', 'services_rating', 'management_rating'];
}