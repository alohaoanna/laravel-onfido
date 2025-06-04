<?php

namespace OANNA\Onfido\Http\Controllers;

use Illuminate\Http\Client\Request;
use Illuminate\Routing\Controller;

class VerificationController extends Controller
{
    public function verification(Request $request)
    {
        $applicantId = $request->applicant_id ?? null;
        $workflowRunId = $request->workflow_run_id ?? null;
        $sdkToken = $request->sdk_token ?? null;

        return view('pages.verification', compact('applicantId', 'workflowRunId', 'sdkToken'));
    }
}
