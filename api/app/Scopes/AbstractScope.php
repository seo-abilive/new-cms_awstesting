<?php
namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;

abstract class AbstractScope implements Scope
{
    protected $enabled = false;

    protected $disabled = [];

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function disableForModel($class)
    {
        $this->disabled[$class] = true;
    }

    public function enableForModel($class)
    {
        $this->disabled[$class] = false;
    }

}
