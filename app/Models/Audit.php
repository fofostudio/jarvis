<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $fillable = [
        'auditor_id',
        'operator_id',
        'conversation_date',
        'review_date',
        'platform',
        'group',
        'girl_id',
        'client_name',
        'client_id',
        'client_status',
        'interesting_greetings',
        'conversation_flow',
        'new_conversation_topics',
        'sentence_structure',
        'generates_love_bond',
        'moderate_gift_request',
        'material_sending',
        'commits_profile',
        'response_times',
        'initiates_hot_chat',
        'conversation_coherence',
        'general_score',
        'general_observation',
        'recommendations',
        'screenshot_paths'
    ];

    protected $casts = [
        'conversation_date' => 'datetime',
        'review_date' => 'datetime',
        'screenshot_paths' => 'array',
        'interesting_greetings' => 'boolean',
        'conversation_flow' => 'boolean',
        'new_conversation_topics' => 'boolean',
        'sentence_structure' => 'boolean',
        'generates_love_bond' => 'boolean',
        'moderate_gift_request' => 'boolean',
        'material_sending' => 'boolean',
        'commits_profile' => 'boolean',
        'response_times' => 'boolean',
        'initiates_hot_chat' => 'boolean',
        'conversation_coherence' => 'boolean',
    ];

    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
    public function girl()
    {
        return $this->belongsTo(Girl::class);
    }
}
