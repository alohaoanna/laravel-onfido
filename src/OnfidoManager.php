<?php

namespace OANNA\Onfido;

use OANNA\Onfido\Models\OnfidoInstance;

class OnfidoManager
{
    private string $model = OnfidoInstance::class;

    public $hasRenderedAssets = false;

    public function markAssetsRendered()
    {
        $this->hasRenderedAssets = true;
    }

    public static function registerOnfidoModel(string $model): void
    {
        app('onfido')->setModel($model);
    }

    public function setModel(string $model)
    {
        $this->model = $class;
    }

    public function getModel(): string
    {
        return $this->model;
    }
}
