<?php

namespace OANNA\Onfido\Repositories;

use OANNA\Onfido\Api\Portal;
use Onfido\ApiException;
use Throwable;

class OnfidoRepository
{
    private $model;
    private $onfido;

    public function __construct($model)
    {
        $this->model = $model;
        $this->onfido = $model?->onfidoInstance;
    }

    /**
     * Start a verification flow
     *
     * @param $region
     * @param array $attributes
     * @param null $workflow
     * @return array|null
     * @throws ApiException
     * @throws Throwable
     */
    public function startVerification($region, $attributes = [], $workflow = null): array|null
    {
        throw_if(empty($this->model), new \Exception("No model provided."));

        throw_if(empty($region), new \Exception("Provide a valid region."));

        $portal = Portal::initialize()->setRegion($region);

        if (empty($this->onfido)) {
            $this->onfido = $this->model->createOnfidoInstance([
                'started' => true,
                'started_at' => now(),
            ]);
        }

        $applicant = $this->onfido->applicant_id ?? null;
        $workflowRun = $this->onfido->workflow_run_id ?? null;
        $sdkToken = $this->onfido->sdk_token ?? null;

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

        return [
            'applicant_id' => $applicant,
            'workflow_id' => $workflow,
            'workflow_run_id' => $workflowRun,
            'sdk_token' => $sdkToken,
        ];
    }
}
