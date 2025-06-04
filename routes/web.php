<?php

\Illuminate\Support\Facades\Route::get('onfido-verification', [\OANNA\Onfido\Http\Controllers\VerificationController::class, 'verification'])->name('onfido.verification');
