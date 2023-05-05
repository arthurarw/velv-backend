<?php

namespace App\Abstract;

abstract class Task
{
    /**
     * @return mixed
     */
    abstract protected function handle(): mixed;

    /**
     * @return mixed
     */
    final public function run(): mixed
    {
        return $this->handle();
    }
}
