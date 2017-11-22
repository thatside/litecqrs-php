<?php

namespace LiteCQRS\Bus;

interface ProxyFactoryInterface
{
    public function __invoke($handler) : MessageHandlerInterface;
}