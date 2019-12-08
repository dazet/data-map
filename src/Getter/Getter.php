<?php declare(strict_types=1);

namespace DataMap\Getter;

use DataMap\Input\Input;

interface Getter
{
    /**
     * @return mixed
     */
    public function __invoke(Input $input);
}
