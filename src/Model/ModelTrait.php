<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 10.03.2018
 * Time: 16:57
 */
declare(strict_types=1);

namespace WPHibou\Readme\Model;

trait ModelTrait
{
    public function __call(string $name, $args)
    {
        if (! isset($args[0])) {
            return $this->__get($name);
        }

        return $this->__set($name, $args[0]);
    }

    public function __get(string $name)
    {
        return $this->{$name};
    }

    public function __set(string $name, $value)
    {
        if (property_exists($this, $name)) {
            if (is_array($this->{$name})) {
                if (! is_array($value)) {
                    $this->$name[] = $value;
                } else {
                    $this->{$name} = array_merge($this->{$name}, $value);
                }
            } else {
                $this->{$name} = $value;
            }
        }

        return $value;
    }
}
