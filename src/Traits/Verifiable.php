<?php

namespace OANNA\Onfido\Traits;


use OANNA\Onfido\Repositories\OnfidoRepository;
use Throwable;
use Carbon\Carbon;
use Onfido\Region;
use Onfido\ApiException;
use Onfido\Model\WorkflowRun;
use OANNA\Onfido\Api\Portal;
use OANNA\Onfido\Models\OnfidoInstance;
use Illuminate\Support\Str;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Application;

/**
 * @property object|null $onfidoInstance
 * @property-read string|null $applicant_id
 * @property-read string|null $workflow_run_id
 * @property-read bool $started
 * @property-read bool $verified
 * @property-read Carbon|null $verification_started_at
 * @property-read Carbon|null $verified_at
 */
trait Verifiable
{
    public function onfidoInstance()
    {
        return $this->morphOne(OnfidoInstance::class, 'model');
    }

    /**
     * Start a now flow for verification
     *
     * @param $region
     * @param array $attributes
     * @param $workflow
     * @return array|null
     * @throws ApiException
     * @throws Throwable
     */
    public function startVerification($region = null, $attributes = [], $workflow = null): array|null
    {
        $repository = new OnfidoRepository($this);

        if (empty($region) && method_exists($this, 'getRegion')) {
            $region = $this->getRegion();
        }

        if (empty($attributes) && method_exists($this, 'getOnfidoAttributes')) {
            $attributes = $this->getOnfidoAttributes();
        }

        return $repository->startVerification($region, $attributes, $workflow);
    }

    /**
     * Refresh the workflow run of the current model
     *
     * @param Region|null $region
     * @param $workflow
     * @return WorkflowRun|null
     * @throws ApiException
     * @throws Throwable
     */
    public function refreshWorkflowRun(Region|null $region = null, $workflow = null): WorkflowRun|null
    {
        $onfidoinstance = $this->onfidoInstance;

        throw_if(empty($onfidoinstance) || empty($onfidoinstance->applicant_id), "refreshWorkflowRun() method is only callable on model that have already started a verification and have an applicant_id.");

        if (empty($region) && method_exists($this, 'getRegion')) {
            $region = $this->getRegion();
        }

        $workflowRun = Portal::initialize()
            ->setApiToken(config('onfido.api.token'))
            ->setRegion($region)
            ->createWorkflowRun($onfidoinstance->applicant_id, $workflow);

        if (empty($workflowRun)) {
            return null;
        }

        $onfidoinstance->update([
            'workflow_id' => $workflowRun->getWorkflowId(),
            'workflow_run_id' => $workflowRun->getId(),
            'sdk_token' => $workflowRun->getSdkToken(),
        ]);

        return $workflowRun;
    }

    public function createOnfidoInstance($attributes = [])
    {
        $class = app('onfido')->getModel();
        return $class::create(array_merge($attributes, [
            'model_id' => $this->id,
            'model_type' => $this::class,
        ]));
    }

    public function isVerified(): bool
    {
        return $this->onfidoInstance?->isVerified() ?? false;
    }

    public function isNotVerified(): bool
    {
        return $this->onfidoInstance?->isNotVerified() ?? false;
    }

    public function isStarted(): bool
    {
        return $this->onfidoInstance?->isStarted() ?? false;
    }

    public function isWaitingApproval(): bool
    {
        return $this->onfidoInstance?->isWaitingApproval() ?? false;
    }

    public function getApplicantIdAttribute()
    {
        return $this->onfidoInstance?->applicant_id;
    }

    public function getWorkflowRunIdAttribute()
    {
        return $this->onfidoInstance?->workflow_run_id;
    }

    public function getStartedAttribute()
    {
        return $this->onfidoInstance?->started;
    }

    public function getVerifiedAttribute()
    {
        return $this->onfidoInstance?->verified;
    }

    public function getVerificationStartedAtAttribute()
    {
        return $this->onfidoInstance?->verification_started_at;
    }

    public function getVerifiedAtAttribute()
    {
        return $this->onfidoInstance?->verified_at;
    }
}
