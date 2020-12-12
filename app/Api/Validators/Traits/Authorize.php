<?php

namespace Api\Validators\Traits;

use Illuminate\Validation\UnauthorizedException;

/**
 * Trait Authorize
 * @package Api\Validators\Traits
 */
trait Authorize
{
    /**
     * @var string
     */
    private $unAuthorizedMessage = '';

    /**
     * @return string
     */
    public function getUnAuthorizedMessage()
    {
        return $this->unAuthorizedMessage;
    }

    /**
     * @param $message
     * @return $this
     */
    public function setUnAuthorizedMessage($message)
    {
        $this->unAuthorizedMessage = $message;

        return $this;
    }

    /**
     * @param $action
     * @return bool
     */
    protected function callAuthorize($action)
    {
        $authorization = 'authorize' . $action;

        if (!method_exists($this, $authorization)) {
            return false;
        }

        if (!$this->{$authorization}()) {
            throw new UnauthorizedException($this->getUnAuthorizedMessage());
        }

        return true;
    }
}
