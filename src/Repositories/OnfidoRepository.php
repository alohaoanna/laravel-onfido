<?php

namespace OANNA\Onfido\Repositories;

use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use OANNA\Onfido\Api\Portal;
use OANNA\Onfido\Models\OnfidoInstance;
use Onfido\ApiException;
use Onfido\Region;
use Throwable;

class OnfidoRepository
{
    private $model;
    private $onfido;

    public function __construct($model = null)
    {
        $this->model = $model;
        $this->onfido = $model?->onfidoInstance;
    }

    /**
     * Start a verification flow
     *
     * @param Region|null $region
     * @param array $attributes
     * @param null $workflow
     * @param mixed $redirection Set it to false if you don't want to redirect user
     * @return Application|Redirector|RedirectResponse|array|null
     * @throws ApiException
     * @throws Throwable
     */
    public function startVerification(Region|null $region, $attributes = [], $workflow = null, $redirection = null): Application|Redirector|RedirectResponse|array|null
    {
        throw_if(empty($region), "Provide a valid region.");

        $applicant = $workflowRun = $sdkToken = null;
        $portal = Portal::initialize()
            ->setApiToken(config('onfido.api.token'))
            ->setRegion($region);

        if (empty($this->onfido)) {
            if (!empty($model)) {
                $this->onfido = $this->model->createOnfidoInstance([
                    'started' => true,
                    'verification_started_at' => now(),
                ]);
            }
            else $this->onfido = OnfidoInstance::create([
                'started' => true,
                'verification_started_at' => now(),
            ]);
        }
        else {
            $applicant = $this->onfido->applicant_id;
            $workflowRun = $this->onfido->workflow_run_id;
            $sdkToken = $this->onfido->sdk_token;
        }

        if (empty($applicant)) {
            $applicant = $portal->createApplicant($attributes);

            if (empty($applicant)) {
                return null;
            }

            $applicant = $applicant->getId();
        }

        if (empty($workflowRun)) {
            $workflowRun = $portal->createWorkflowRun($applicant, $workflow);

            if (empty($workflowRun)) {
                return null;
            }

            $sdkToken = $workflowRun->getSdkToken();
            $workflow = $workflowRun->getWorkflowId();
            $workflowRun = $workflowRun->getId();
        }

        $this->onfido->update([
            'applicant_id' => $applicant,
            'workflow_id' => $workflow,
            'workflow_run_id' => $workflowRun,
            'sdk_token' => $sdkToken,
        ]);

        if (config('onfido.livewire', true)) {
            $component = app('livewire')->current();
            if ($component) {
                $component->disaptch('onfido.verification.start');
            }
        }

        $redirection = is_null($redirection) ? config('onfido.redirection', true) : false;
        if ($redirection === false || $redirection === null) {
            return ['onfido_instance_id' => $this->onfido->id, 'applicant_id' => $applicant, 'workflow_run_id' => $workflowRun, 'sdkToken' => $sdkToken];
        }
        else if (is_string($redirection)) {
            return redirect(route($redirection, ['onfido_instance_id' => $this->onfido->id, 'applicant_id' => $applicant, 'workflow_run_id' => $workflowRun, 'sdkToken' => $sdkToken]));
        }

        return redirect(route('onfido.verification', ['onfido_instance_id' => $this->onfido->id, 'applicant_id' => $applicant, 'workflow_run_id' => $workflowRun, 'sdkToken' => $sdkToken]));
    }
}
