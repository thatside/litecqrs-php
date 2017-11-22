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

use LiteCQRS\Saga\State\Criteria;

interface MetadataInterface
{
    /**
     * @param mixed $event
     *
     * @return boolean True, if the saga can handle the event
     */
    public function handles($event);

    /**
     * @param mixed $event
     *
     * @return Criteria Criteria for the given event
     */
    public function criteria($event);
}
