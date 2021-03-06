<?php

/*
 * This file is part of the broadway/broadway-saga package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LiteCQRS\Saga\Metadata;

use LiteCQRS\Saga\SagaInterface;

/**
 * @todo: ?? :D
 */
interface StaticallyConfiguredSagaInterface extends SagaInterface
{
    public static function configuration();
}
