<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable=['short_des','terms_conditions','refund_policy','privacy_policy','description','photo','address','phone','email','logo'];
}
