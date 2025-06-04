<?php

namespace OANNA\Onfido;

use function Livewire\on;

class OnfidoManager
{
    public $hasRenderedAssets = false;

    public function boot()
    {
        on('flush-state', function () {
           $this->hasRenderedAssets = false;
        });
    }

    public function markAssetsRendered()
    {
        $this->hasRenderedAssets = true;
    }

    public function scripts($options = [])
    {
        $this->markAssetsRendered();

        return AssetManager::scripts($options);
    }

    public function assets($options = [])
    {
        $this->markAssetsRendered();

        return AssetManager::assets($options);
    }
}
