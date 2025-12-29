<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LabelTask extends Pivot
{
    use HasUuids;

    protected $table = 'label_task';

    public $incrementing = false;

    protected $keyType = 'string';
}
