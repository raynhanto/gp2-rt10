<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\AdminActivityLog;

trait LogsAdminActivity
{
    protected function logActivity(
        string $action,
        string $description,
        ?string $modelType = null,
        mixed $modelId = null,
        array $context = []
    ): void {
        try {
            AdminActivityLog::record(
                $action,
                $description,
                $modelType,
                $modelId !== null ? (int) $modelId : null,
                $context
            );
        } catch (\Throwable) {
            // Never let logging crash a request
        }
    }
}
