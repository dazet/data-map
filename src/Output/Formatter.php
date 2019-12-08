<?php declare(strict_types=1);

namespace DataMap\Output;

interface Formatter
{
	/**
     * @param array<string, mixed> $output
	 * @return mixed
	 */
	public function format(array $output);
}
