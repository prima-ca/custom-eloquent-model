<?php

namespace Cyrus\Nova\Http\Requests;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Http\FormRequest;

class NovaRequestWithContainer extends \Laravel\Nova\Http\Request;
//class NovaRequestWithContainer extends \Laravel\Nova\Http\Requests\ResourceIndexRequest
{
    /**
     * Get the container implementation.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return $this
     */
    public function getContainer()
    {
        return $this->container;
    }

}
