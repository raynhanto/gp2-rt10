<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GsheetSyncLog extends Model
{
    public $timestamps = false;

    protected $table = 'gsheet_sync_log';

    protected $fillable = ['tab', 'rows_written', 'status', 'error_msg', 'duration_ms'];
}
