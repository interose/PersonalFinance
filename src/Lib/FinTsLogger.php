<?php

namespace App\Lib;

use Psr\Log\AbstractLogger;

class FinTsLogger extends AbstractLogger
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     *
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function log($level, $message, array $context = []): void
    {
        file_put_contents(__DIR__.'/../../state.log', file_get_contents(__DIR__.'/../../state.log').$message."\n");
    }
}
