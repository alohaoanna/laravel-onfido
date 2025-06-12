<?php

namespace OANNA\Onfido\Api;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Onfido\Api\DefaultApi;
use Onfido\ApiException;
use Onfido\Configuration;
use Onfido\Model\Applicant;
use Onfido\Model\ApplicantBuilder;
use Onfido\Model\Error;
use Onfido\Model\WorkflowRun;
use Onfido\Model\WorkflowRunBuilder;
use Onfido\Region;

class Portal
{
    private DefaultApi $api;
    private Client $client;
    private Configuration $configuration;

    public function __construct($configuration = null)
    {
        $this->configuration = $configuration ?? Configuration::getDefaultConfiguration();

        $this->configuration->setApiToken(config('onfido.api.token'))
            ->setRegion(self::regionFromString(config('onfido.api.region', Region::EU)));

        $this->client = self::getDefaultClient();

        $this->api = new DefaultApi(
            $this->client,
            $this->configuration,
        );
    }

    public static function initialize($configuration = null): static
    {
        return new static($configuration);
    }

    public function setApiToken(string $apiToken): self
    {
        $this->configuration->setApiToken($apiToken);
        return $this->setApi($this->client);
    }

    public function setRegion(Region $region): self
    {
        $this->configuration->setRegion($region);
        return $this->setApi($this->client);
    }

    public function setApi($client = null): self
    {
        $this->api = new DefaultApi(
            $client ?? self::getDefaultClient(),
            $this->configuration,
        );
        return $this;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public static function getDefaultClient($timeout = null, $connect_timeout = null, $read_timeout = null): Client
    {
        return new Client([
            'timeout' => $timeout ?? config('onfido.api.timeout.default', 30),
            'connect_timeout' => $connect_timeout ?? config('onfido.api.timeout.connect', 30),
            'read_timeout' => $read_timeout ?? config('onfido.api.timeout.read', 30),
        ]);
    }

    /**
     * Create an applicant
     *
     * @param array $attributes attributes of the new applicant
     * @return Applicant|null
     * @throws ApiException
     * @throws \Throwable
     */
    public function createApplicant(array $attributes = []): Applicant|null
    {
        throw_if(empty($attributes), 'The $attributes params is empty. Please provide at least a firstname and lastname.');

        return $this->execute(function () use ($attributes) {
            $applicantBuilder = new ApplicantBuilder([
                'first_name' => $attributes['first_name'] ?? config('onfido.dataset.default.first_name', 'John'),
                'last_name' => $attributes['last_name'] ?? config('onfido.dataset.default.last_name', "Doe"),
                'dob' => $attributes['dob'] ?? config('onfido.dataset.default.dob', "2000-01-01"),
            ]);

            $applicant = $this->api->createApplicant($applicantBuilder);

            if ($applicant instanceof Error) {
                throw new ApiException($applicant->getError());
            }

            return $applicant;
        });
    }

    /**
     * Create a workflow run
     *
     * @param Applicant|string $applicant
     * @param string $workflow
     * @return WorkflowRun|null
     * @throws ApiException
     * @throws \Throwable
     */
    public function createWorkflowRun($applicant, $workflow = null): WorkflowRun|null
    {
        if ($applicant instanceof Applicant) {
            $applicant = $applicant->getId();
        }

        $workflow ??= config('onfido.api.workflow_id');

        throw_if(empty($applicant), 'Please provide a valid applicant.');

        throw_if(empty($workflow), 'Please provide a valid workflow id.');

        return $this->execute(function () use ($applicant, $workflow) {
            $workflowRunBuilder = new WorkflowRunBuilder([
                'applicant_id' => $applicant,
                'workflow_id' => $workflow,
            ]);

            $workflowRun = $this->api->createWorkflowRun($workflowRunBuilder);

            if ($workflowRun instanceof Error) {
                throw new ApiException($workflowRun->getError());
            }

            return $workflowRun;
        });
    }

    public function execute($fallback): mixed
    {
        try {
            return $fallback();
        }
        catch (ApiException $e) {
            if (app()->environment('local')) {
                throw $e;
            }
            else {
                Log::error("[".now()->format('Y-m-d H:i:s')."] [ApiException] ERROR : {$e->getMessage()} \n {$e->getTraceAsString()}");
                $mail = config('onfido.debug.mail');

                if (! empty($mail)) {
                    mail($mail, "ONFIDO ERROR", "{$e->getMessage()} \n {$e->getTraceAsString()}");
                }
            }
        }

        return null;
    }

    public static function regionFromString(string $key)
    {
        return match(strtoupper($key)) {
            'CA' => Region::CA,
            'US' => Region::US,
            default => Region::EU,
        };
    }
}
