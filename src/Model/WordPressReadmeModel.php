<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 10.03.2018
 * Time: 16:44
 */
declare(strict_types=1);

namespace WPHibou\Readme\Model;

/**
 * Class WordPressReadmeModel
 * @package RZF\Theme\Models
 */
final class WordPressReadmeModel implements ReadmeModelInterface
{
    use ModelTrait;
    use WordPressModelTrait;
    use WordPressModelDefaultsTrait;
}
