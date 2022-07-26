<?php
namespace verbb\patrol\variables;

use verbb\patrol\Patrol;

class PatrolVariable
{
    // Public Methods
    // =========================================================================

    public function getPluginName(): string
    {
        return Patrol::$plugin->getPluginName();
    }
}
