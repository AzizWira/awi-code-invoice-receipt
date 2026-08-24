<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    public function log(string $action, Model $subject, string $summary, ?array $before = null, ?array $after = null): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject::class,
            'subject_id' => $subject->getKey(),
            'summary' => $summary,
            'before_data' => $before,
            'after_data' => $after,
        ]);
    }
}
