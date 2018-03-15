<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 10.03.2018
 * Time: 17:31
 */

namespace WPHibou\Readme\Exception;

class InvalidReadmeFileException extends \InvalidArgumentException
{

    /**
     * InvalidModelException constructor.
     *
     * @param string $model
     */
    public function __construct(string $model)
    {
        $this->message = sprintf('Readme file %s not found.', $model);
    }
}
