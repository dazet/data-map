<?php

namespace DataMap\Input;

interface ExtensibleWrapper extends Wrapper
{
    public function withWrappers(Wrapper ...$wrappers): ExtensibleWrapper;
}
