<?php

namespace OANNA\Onfido\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OANNA\Onfido\Enums\Status;

/**
 * @property-read Status $status
 * @property string|null $applicant_id
 * @property string|null $workflow_id
 * @property string|null $workflow_run_id
 * @property string|null $sdk_token
 * @property bool $started
 * @property bool $verified
 * @property Carbon|null $started_at
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
        'started_at',
        'verified_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function isVerified(): bool
    {
        return $this->verified === true && (! is_null($this->verified_at) && $this->verified_at->isPast());
    }

    public function isNotVerified(): bool
    {
        return $this->verified !== true && (is_null($this->verified_at) || $this->verified_at->isFuture());
    }

    public function isStarted(): bool
    {
        return $this->started === true && (! is_null($this->started_at) && $this->started_at->isPast());
    }

    public function isWaitingApproval(): bool
    {
        return is_null($this->verified) && (! is_null($this->verified_at) && $this->verified_at->isPast());
    }

    public function start($date = null): bool
    {
        $date = $date ?? now();

        return $this->update([
            'started' => true,
            'started_at' => $date,
        ]);
    }

    public function startNow(): bool
    {
        return $this->update([
            'started' => true,
            'started_at' => now(),
        ]);
    }

    public function verify(): bool
    {
        return $this->update([
            'verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function unverify(): bool
    {
        return $this->update([
            'verified' => false,
            'verified_at' => null,
        ]);
    }

    public function reset(bool $hard = false): bool
    {
        return $this->update([
            'applicant_id' => $hard === true ? null : $this->applicant_id,
            'workflow_id' => null,
            'workflow_run_id' => null,
            'sdk_token' => null,
            'started' => null,
            'started_at' => null,
            'verified' => null,
            'verified_at' => null,
        ]);
    }

    public function getStatusAttribute()
    {
        return $this->getStatus();
    }

    public function getStatus(): Status
    {
        return match (true) {
            $this->isStarted() && $this->isVerified() => Status::VERIFIED,
            $this->isStarted() && $this->isNotVerified() => Status::NOT_VERIFIED,
            $this->isStarted() && $this->isNotVerified() && ! $this->isWaitingApproval() => Status::STARTED,
            $this->isStarted() && $this->isNotVerified() && $this->isWaitingApproval() => Status::WAITING,
            default => Status::UNDEFINED,
        };
    }
}
