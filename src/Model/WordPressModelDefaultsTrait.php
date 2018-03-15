<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 11.03.2018
 * Time: 10:06
 */
declare(strict_types=1);

namespace WPHibou\Readme\Model;

trait WordPressModelDefaultsTrait
{
    private $is_excerpt = false;
    private $is_truncated = false;
    private $tags = [];
    private $requires = '';
    private $requires_php = '';
    private $tested = '';
    private $contributors = [];
    private $stable_tag = '4.9.4';
    private $version = '';
    private $donate_link = '';
    private $short_description = '';
    private $sections = [
        'description' => '',
        'installation' => '',
        'faq' => '',
        'screenshots' => '',
        'changelog' => '',
        'upgrade_notice' => '',
        'other_notes' => '',
    ];
    private $alias_sections = [
        'frequently_asked_questions' => 'faq',
        'change_log'                 => 'changelog',
        'screenshot'                 => 'screenshots',
    ];
    private $changelog = [];
    private $upgrade_notice = [];
    private $screenshots = [];
    private $remaining_content = [];
    private $name = '';
    private $valid_headers = [
        'tested'            => 'tested',
        'tested up to'      => 'tested',
        'requires'          => 'requires',
        'requires at least' => 'requires',
        'requires php'      => 'requires_php',
        'tags'              => 'tags',
        'contributors'      => 'contributors',
        'donate link'       => 'donate_link',
        'stable tag'        => 'stable_tag',
        'license'           => 'license',
        'license uri'       => 'license_uri',
    ];

    public $license = '';

    public $license_uri = '';

    public $faq = [];

    private $ignore_tags = [
        'plugin',
        'wordpress',
    ];
}
