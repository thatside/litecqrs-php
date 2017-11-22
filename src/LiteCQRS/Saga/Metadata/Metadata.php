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
use RuntimeException;

class Metadata implements MetadataInterface
{
    private $criteria;

    /**
     * @param array $criteria
     */
    public function __construct( $criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * {@inheritDoc}
     */
    public function handles($event) : bool
    {
        $eventName = $this->getClassName($event);

        return isset($this->criteria[$eventName]);
    }

    /**
     * {@inheritDoc}
     */
    public function criteria($event) : ?Criteria
    {
        $eventName = $this->getClassName($event);

        if (! isset($this->criteria[$eventName])) {
            throw new RuntimeException(sprintf("No criteria for event '%s'.", $eventName));
        }

        return $this->criteria[$eventName]($event);
    }

    private function getClassName($event) : string
    {
        $classParts = explode('\\', get_class($event));

        return end($classParts);
    }
}
