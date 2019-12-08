<?php declare(strict_types=1);

namespace DataMap\Input;

interface ExtensibleWrapper extends Wrapper
{
    public function withWrappers(Wrapper ...$wrappers): ExtensibleWrapper;
}
