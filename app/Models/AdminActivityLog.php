<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminActivityLog extends Model
{
    public $timestamps = false;

    protected $table = 'admin_activity_log';

    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'description', 'context', 'ip',
    ];

    protected function casts(): array
    {
        return ['context' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        string $action,
        string $description,
        ?string $modelType = null,
        ?int $modelId = null,
        array $context = []
    ): void {
        static::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'description' => $description,
            'context'     => $context ?: null,
            'ip'          => request()->ip(),
        ]);
    }
}
