<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity;

class AdminLog extends Activity
{
    protected $table = 'admin_logs';
}
