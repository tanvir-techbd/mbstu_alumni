<?php

namespace App\Enums;

enum FeedbackType: string
{
    case Suggestion = 'suggestion';
    case Complaint = 'complaint';
    case FeatureRequest = 'feature-request';

    public function label(): string
    {
        return match ($this) {
            self::Suggestion => 'Suggestion',
            self::Complaint => 'Complaint',
            self::FeatureRequest => 'Feature Request',
        };
    }
}
