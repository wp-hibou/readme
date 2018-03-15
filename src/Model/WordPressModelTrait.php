<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 10.03.2018
 * Time: 21:21
 */
declare(strict_types=1);

namespace WPHibou\Readme\Model;

trait WordPressModelTrait
{
    public function changelog(array $changelog = []): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function contributors(array $contributors = []): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function donate_link(string $donate_link = ''): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function is_excerpt(bool $is_excerpt = false): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function is_truncated(bool $is_truncated = false): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function name(string $name = ''): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function remaining_content(array $remaining_content = []): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function requires(array $requires = []): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function screenshots(array $screenshots = []): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function sections(array $sections = []): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function short_description(string $short_description = ''): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function stable_tag(string $stable_tag = ''): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function tags(array $tags = []): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function tested(string $tested = ''): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function upgrade_notice(array $upgrade_notice = []): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function version(string $version = ''): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}
