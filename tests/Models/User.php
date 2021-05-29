<?php

namespace Smartisan\Settings\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Smartisan\Settings\HasSettings;

class User extends Model
{
    use HasSettings;

    protected $guarded = [];
}
