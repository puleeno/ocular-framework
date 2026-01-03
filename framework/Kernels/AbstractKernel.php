<?php
namespace Ocular\Framework\Kernels;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ocular\Framework\Http\StateResetter;

abstract class AbstractKernel
{
    protected StateResetter $resetter;
    protected array $bootloaders = [];

    public function __construct(StateResetter $resetter)
    {
        $this->resetter = $resetter;
    }

    abstract public function bootstrap(): void;

    abstract public function handle(ServerRequestInterface $request): ResponseInterface;

    public function terminate(): void
    {
        $this->resetter->reset();
    }
}
