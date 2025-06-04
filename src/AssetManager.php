<?php

namespace OANNA\Onfido;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

class AssetManager
{
    static function boot()
    {
        $instance = new static;

        $instance->registerAssetDirective();
    }

    private function registerAssetDirective(): void
    {
        Blade::directive('onfidoAssets', function ($expression) {
            return <<<PHP
            {!! app('onfido')->assets($expression) !!}
            PHP;
        });

        Blade::directive('onfidoScripts', function ($expression) {
            return <<<PHP
            {!! app('onfido')->scripts($expression) !!}
            PHP;
        });
    }

    public static function scripts($option = [])
    {
        $livewire = config('onfido.livewire', true);

        $scripts = '';

        if (strtolower($option[0]) === 'npm') {
            $scripts .= '<script>' . <<<JS
                import { Onfido } from 'onfido-sdk-ui';
                window.Onfido = window.onfido = Onfido;
            JS . '</script>';
        }

        $scripts .= '<script>' . <<<JS
            if (Onfido) {
                window.Onfido = Onfido;
            }

            var livewire = $livewire
            window.$onfido = {
                init(containerId, sdkToken, workflowRunId) {
                    window.Onfido.init({
                        token: sdkToken,
                        containerId: containerId,
                        onComplete: function (data) {
                            console.log("[Onfido] Flow completed!");
                            console.log("[Onfido] datas:", data);

                            if (livewire) {
                                window.Livewire?.first().dispatch('onfido.workflow.complete', JSON.stringify(data));
                            }
                        },
                        onError: function (error) {
                            console.error("[Onfido] An error occurred");
                            console.error("[Onfido] error:", "[" + error.type + "] " + error.message);

                            if (livewire) {
                                window.Livewire?.first().dispatch('onfido.workflow.error', JSON.stringify(error));
                            }
                        },
                        workflowRunId: workflowRunId,
                    });
                }
            };
        JS;

        $scripts .= '</script>';

        return $scripts;
    }

    public static function assets($option = [])
    {
        return '<script src="https://sdk.onfido.com/'.config('onfido.sdk.version', '14.46.1').'" charset="utf-8"></script>';
    }
}
