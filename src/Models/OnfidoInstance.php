<?php

namespace OANNA\Onfido\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string|null $applicant_id
 * @property string|null $workflow_id
 * @property string|null $workflow_run_id
 * @property string|null $sdk_token
 * @property bool $started
 * @property bool $verified
 * @property Carbon|null $verification_started_at
 * @property Carbon|null $verified_at
 */
class OnfidoInstance extends Model
{
    protected $table = 'onfido_instances';

    protected $fillable = [
        'applicant_id',
        'workflow_id',
        'workflow_run_id',
        'sdk_token',
        'started',
        'verified',
        'verification_started_at',
        'verified_at',
    ];

    protected $casts = [
        'verification_started_at' => 'datetime',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function isVerified(): bool
    {
        return $this->verified && (! is_null($this->verified_at) && $this->verified_at->isPast());
    }

    public function isNotVerified(): bool
    {
        return !$this->isVerified();
    }

    public function isStarted(): bool
    {
        return $this->started && (! is_null($this->verification_started_at) && $this->verification_started_at->isPast());
    }

    public function isWaitingApproval(): bool
    {
        return !$this->verified && (! is_null($this->verification_started_at) && $this->verification_started_at->isPast());
    }
}
