<?php

namespace OANNA\Onfido\Models;

use Illuminate\Database\Eloquent\Model;
use OANNA\Onfido\Traits\Verifiable;

class User extends Model
{
    use Verifiable;
}
