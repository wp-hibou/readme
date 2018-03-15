<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 10.03.2018
 * Time: 16:43
 */
declare(strict_types=1);

namespace WPHibou\Readme\Model;

interface ReadmeModelInterface
{
    public function changelog(array $changelog = []): array;

    public function contributors(array $contributors = []): array;

    public function donate_link(string $donate_link = ''): string;

    public function is_excerpt(bool $is_excerpt = false): bool;

    public function is_truncated(bool $is_truncated = false): bool;

    public function name(string $name = ''): string;

    public function remaining_content(array $remaining_content = []): array;

    public function requires(string $requires = ''): string;

    public function requires_php(string $requires_php = ''): string;

    public function screenshots(array $screenshots = []): array;

    public function sections(array $sections = []): array;

    public function short_description(string $short_description = ''): string;

    public function stable_tag(string $stable_tag = ''): string;

    public function tags(array $tags = []): array;

    public function tested(string $tested = ''): string;

    public function upgrade_notice(array $upgrade_notice = []): array;

    public function version(string $version = ''): string;

    public function license(string $license = ''): string;

    public function license_uri(string $license_uri = ''): string;

    public function faq(array $faq = []): array;
}
