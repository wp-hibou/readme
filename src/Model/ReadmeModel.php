<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 10.03.2018
 * Time: 16:42
 */
declare(strict_types=1);

namespace WPHibou\Readme\Model;

class ReadmeModel
{
    public $is_excerpt = false;
    public $is_truncated = false;
    public $tags = [];
    public $requires = '';
    public $tested = '';
    public $contributors = [];
    public $stable_tag = '';
    public $version = '';
    public $donate_link = '';
    public $short_description = '';
    public $sections = [];
    public $changelog = [];
    public $upgrade_notice = [];
    public $screenshots = [];
    public $remaining_content = [];
}
