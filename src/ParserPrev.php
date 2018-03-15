<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 10.03.2018
 * Time: 16:40
 */
declare(strict_types=1);

namespace WPHibou\Readme;

use WPHibou\Readme\Exception\InvalidModelException;
use WPHibou\Readme\Exception\InvalidReadmeFileException;
use WPHibou\Readme\Model\ReadmeModelInterface;

class ParserPrev
{
    /**
     * @var ReadmeModelInterface $model
     */
    protected $model;
    protected $file;
    protected $contents;
    protected $modelRef;
    protected $modelNamespace = __NAMESPACE__ . '\\Model\\';
    protected $screenShotUrl = 'https://ps.w.org/%s/assets/screenshot-%s.%s';
    protected $checkScreenShotUrl;

    public function __construct(string $model, string $file, bool $checkScreenShotUrl = false)
    {
        try {
            $model          = class_exists($model) ? $model : $this->modelNamespace . $model;
            $this->modelRef = new \ReflectionClass($model);
            $this->model    = $this->modelRef->newInstanceWithoutConstructor();
            if (! $this->modelRef->implementsInterface($this->modelNamespace . 'ReadmeModelInterface')) {
                throw new InvalidModelException(
                    sprintf('Readme Model %s must implement ReadmeModelInterface', $model)
                );
            }
        } catch (\ReflectionException $e) {
            throw new InvalidModelException(sprintf('%s is not a valid Readme Model', $model));
        }

        if (! file_exists($file)) {
            throw new InvalidReadmeFileException($file);
        }
        $this->file = $file;

        $this->checkScreenShotUrl = $checkScreenShotUrl;
    }

    public function parse()
    {
        $this->parseContents();

        return $this->model;
    }

    private function parseContents()
    {
        $this->normalizeContents();

        $name = $this->getFirstNonWhitespace();
        $this->model->name($this->sanitizeText(trim($name, "#= ")));

        // Parse headers
        $this->setHeaders($this->getHeaders());

        // Parse the short description
        $shortDescription = $this->parseShortDescription();
        $this->model->short_description($shortDescription);
        $isTruncated = $this->trimShortDesc($shortDescription);

        $this->model->is_truncated($isTruncated);

        // Parse the rest of the body
        $this->parseBody();

        $this->parseScreenshots();
    }

    private function normalizeContents()
    {
        $this->contents = file_get_contents($this->file);
        $regex          = preg_match('!!u', $this->contents) ? '!\R!u' : '!\R!';
        $this->contents = preg_split($regex, $this->contents);
        $this->contents = array_map([$this, 'stripNewLines'], $this->contents);

        // Strip BOM
        if (strpos($this->contents[0], "\xEF\xBB\xBF") === 0) {
            $this->contents[0] = substr($this->contents[0], 3);
        }

        // Convert UTF-16 files.
        if (strpos($this->contents[0], "\xFF\xFE") === 0) {
            foreach ($this->contents as $i => $line) {
                $this->contents[$i] = mb_convert_encoding($line, 'UTF-8', 'UTF-16');
            }
        }

        return $this->contents;
    }

    protected function getFirstNonWhitespace()
    {
        while (($line = array_shift($this->contents)) !== null) {
            $line = trim($line);
            if (! empty($line)) {
                break;
            }
        }

        return $line;
    }

    private function setHeaders(array $headers)
    {
        foreach ($headers as $header => $value) {
            if (! $this->modelRef->hasMethod($header)) {
                continue;
            }
            if ((string)$this->modelRef->getMethod($header)->getParameters()[0]->getType() === 'array') {
                $value = array_map('trim', explode(',', $value));
                $this->model->{$header}($value);
                continue;
            }

            $this->model->{$header}($value);
        }
    }

    private function getHeaders(): array
    {
        $headers = [];

        $line = $this->getFirstNonWhitespace();
        do {
            $key = $value = null;
            if (strpos($line, ':') === false) {
                break;
            }
            $bits = explode(':', $line, 2);
            list($key, $value) = $bits;
            $key = strtolower(str_replace([' ', "\t"], '_', trim($key)));
            if ($key === 'tags' && isset($headers['tags'])) {
                $headers[$key] .= ',' . trim($value);
            } else {
                $headers[$key] = trim($value);
            }
        } while (($line = array_shift($this->contents)) !== null && ($line = trim($line)) && ! empty($line));
        array_unshift($this->contents, $line);

        return $headers;
    }

    private function parseShortDescription(): string
    {
        $shortDescription = '';

        while (($line = array_shift($this->contents)) !== null) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                $shortDescription .= "\n";
                continue;
            }
            if ($trimmed[0] === '=' && isset($trimmed[1]) && $trimmed[1] === '=') {
                array_unshift($this->contents, $line);
                break;
            }

            $shortDescription .= $line . "\n";
        }

        return trim($shortDescription);
    }

    protected function trimShortDesc(string &$desc): bool
    {
        if (mb_strlen($desc) > 150) {
            if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                $desc = mb_substr($desc, 0, 150);
            } else {
                $desc = substr($desc, 0, 150);
            }
            $desc = trim($desc);

            return true;
        }

        return false;
    }

    private function parseBody()
    {
        $current = '';
        while (($line = array_shift($this->contents)) !== null) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                $current .= "\n";
                continue;
            }

            if (substr($trimmed, 0, 2) === '==') {
                $current   = '';
                $realTitle = trim($line, "#= \t");
                $title     = strtolower(str_replace(' ', '_', $realTitle));

                if ($title === 'faq') {
                    $title     = 'frequently_asked_questions';
                    $realTitle = 'Frequently Asked Questions';
                }

                if ($title === 'change_log') {
                    $title     = 'changelog';
                    $realTitle = 'Changelog';
                }

                if (! in_array($title, $this->sections)) {
                    $current .= sprintf('<h3>%s</h3>', $realTitle);
                }

                continue;
            }

            $current .= $line . "\n";

            if (! empty($title) && ! empty($current)) {
                $content                       = trim($current);
                $this->model->sections[$title] = $this->parseMarkdown($content);
                if ($this->modelRef->hasMethod($title)) {
                    $this->model->{$title}($this->parseSection($title, $content));
                }
            }
        }

        if (empty($this->model->sections['description'])) {
            $this->model->sections['description'] = $this->parseMarkdown($this->model->short_description());
        }
    }

    protected function parseMarkdown(string $text): string
    {
        $text = $this->parseCodeTags($text);
        $text = preg_replace('/^[\s]*=[\s]+(.+?)[\s]+=/m', "\n" . '<h4>$1</h4>' . "\n", $text);
        $text = Markdown(trim($text));

        return trim($text);
    }

    protected function parseCodeTags(string $text): string
    {
        // Convert code blocks to backticks
        $text = preg_replace_callback(
            "!(<pre><code>|<code>)(.*?)(</code></pre>|</code>)!s",
            [$this, 'decode'],
            $text
        );
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        // Markdown can do inline code, we convert bbPress style block level code to Markdown style
        $text = preg_replace_callback("!(^|\n)([ \t]*?)`(.*?)`!s", [$this, 'indent'], $text);

        return $text;
    }

    private function parseSection(string $title, string $contents)
    {
        $return  = [];
        $current = '';
        $lines   = explode("\n", $contents);
        while (($line = array_shift($lines)) !== null) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                continue;
            }

            if ($trimmed[0] === '=') {
                $current = '';
                $title   = trim($line, "#= \t");
                continue;
            }


            $current .= $line . "\n";
            if (! empty($current) && ! empty($title)) {
                $return[$title] = trim($current);
            }
        }

        return $return;
    }

    private function parseScreenshots()
    {
        if (! empty($this->model->sections['screenshots'])) {
            preg_match_all('#<li>(.*?)</li>#is', $this->model->sections['screenshots'], $screenshots, PREG_SET_ORDER);
            if ($screenshots) {
                $i = 0;
                unset($this->model->screenshots['screenshots']);
                foreach ((array)$screenshots as $ss) {
                    if ($this->checkScreenShotUrl) {
                        if (($url = $this->screenShotUrl(++$i))) {
                            $caption = trim($ss[1]);
                            $this->model->screenshots([
                                [
                                    'caption' => $caption,
                                    'url'     => $url,
                                ],
                            ]);
                        }
                    } else {
                        $this->model->screenshots([$ss[1]]);
                    }
                }

                if ($this->checkScreenShotUrl) {
                    $this->parseScreenshotsMarkdown();
                }
            }
        }
    }

    private function screenShotUrl(int $i): string
    {
        $extensions = ['png', 'jpg', 'jpeg', 'gif'];
        foreach ($extensions as $ext) {
            $file    = sprintf($this->screenShotUrl, $this->getSlug(), $i, $ext);
            $headers = @get_headers($file);
            if (empty($headers[0])) {
                continue;
            }
            if (strpos($headers[0], '200') !== false) {
                return $file;
            }
        }

        return '';
    }

    private function getSlug(): string
    {
        return sanitize_title($this->model->name());
    }

    private function parseScreenshotsMarkdown()
    {
        $html = '<ol>';
        foreach ($this->model->screenshots() as $screenshot) {
            $html .= sprintf(
                '<li><figure><img src="%s" alt=""><figcaption>%s</figcaption></figure></li>',
                $screenshot['url'],
                $screenshot['caption']
            );
        }
        $html .= '</ol>';

        $this->model->sections(['screenshots' => $html]);
    }

    protected function sanitizeText(string $text): string
    {
        return trim(esc_html(strip_tags($text)));
    }

    protected function stripNewLines(string $line): string
    {
        return rtrim($line, "\r\n");
    }

    protected function indent(array $matches): string
    {
        $text = $matches[3];
        $text = preg_replace('|^|m', $matches[2] . '    ', $text);

        return $matches[1] . $text;
    }

    protected function decode(array $matches): string
    {
        $text        = $matches[2];
        $trans_table = array_flip(get_html_translation_table(HTML_ENTITIES));
        $text        = strtr($text, $trans_table);
        $text        = str_replace('<br />', '', $text);
        $text        = str_replace('&#38;', '&', $text);
        $text        = str_replace('&#39;', "'", $text);
        if ('<pre><code>' === $matches[1]) {
            $text = "\n$text\n";
        }

        return "`$text`";
    }
}
