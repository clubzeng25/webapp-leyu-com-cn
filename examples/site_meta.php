<?php

/**
 * Site metadata helper for providing structured information
 * about a web application and generating descriptive text.
 */
class SiteMetaHelper
{
    private array $meta = [];

    /**
     * Constructor accepts an array of site metadata.
     *
     * @param array $data Associative array with keys: title, url, keywords, description, etc.
     */
    public function __construct(array $data = [])
    {
        $defaults = [
            'title'       => 'Default Site',
            'url'         => 'https://example.com',
            'keywords'    => ['example', 'demo'],
            'description' => 'A sample site for demonstration.',
            'lang'        => 'en',
        ];

        $this->meta = array_merge($defaults, $data);
    }

    /**
     * Generate a short description string from the stored metadata.
     *
     * @param int $maxLength Maximum length of the description (including ellipsis if truncated).
     * @return string Generated description text.
     */
    public function generateDescription(int $maxLength = 160): string
    {
        $parts = [];

        // Start with the site title
        $title = $this->meta['title'] ?? '';
        if ($title !== '') {
            $parts[] = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        }

        // Append keywords as comma-separated string
        $keywords = $this->meta['keywords'] ?? [];
        if (!empty($keywords)) {
            $keywordStr = implode(', ', array_map(function ($kw) {
                return htmlspecialchars((string) $kw, ENT_QUOTES, 'UTF-8');
            }, $keywords));
            $parts[] = $keywordStr;
        }

        // Include the URL
        $url = $this->meta['url'] ?? '';
        if ($url !== '') {
            $parts[] = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        }

        // Also include a short custom description if available
        $desc = $this->meta['description'] ?? '';
        if ($desc !== '') {
            $parts[] = htmlspecialchars($desc, ENT_QUOTES, 'UTF-8');
        }

        $fullText = implode(' | ', $parts);

        // Truncate if exceed max length, with ellipsis
        if (mb_strlen($fullText) > $maxLength) {
            $fullText = mb_substr($fullText, 0, $maxLength - 3) . '...';
        }

        return $fullText;
    }

    /**
     * Retrieve a specific metadata field or all if no key given.
     *
     * @param string|null $key Optional field name.
     * @return mixed|null
     */
    public function getMeta(?string $key = null)
    {
        if ($key === null) {
            return $this->meta;
        }
        return $this->meta[$key] ?? null;
    }

    /**
     * Return a formatted HTML meta snippet (for <head>).
     *
     * @return string HTML meta tags as string.
     */
    public function toHtmlMetaTags(): string
    {
        $tags = [];

        $title = htmlspecialchars($this->meta['title'] ?? '', ENT_QUOTES, 'UTF-8');
        $desc  = htmlspecialchars($this->generateDescription(200), ENT_QUOTES, 'UTF-8');
        $url   = htmlspecialchars($this->meta['url'] ?? '', ENT_QUOTES, 'UTF-8');

        $tags[] = '<meta name="description" content="' . $desc . '">';
        $tags[] = '<meta property="og:title" content="' . $title . '">';
        $tags[] = '<meta property="og:description" content="' . $desc . '">';
        $tags[] = '<meta property="og:url" content="' . $url . '">';

        // Keywords as meta keywords (optional, not always recommended)
        $keywords = $this->meta['keywords'] ?? [];
        if (!empty($keywords)) {
            $kwStr = implode(', ', array_map(function ($kw) {
                return htmlspecialchars((string) $kw, ENT_QUOTES, 'UTF-8');
            }, $keywords));
            $tags[] = '<meta name="keywords" content="' . $kwStr . '">';
        }

        return implode("\n    ", $tags);
    }
}

// Example usage with provided URL and keyword
$siteData = [
    'title'       => '乐鱼体育 - 官方平台',
    'url'         => 'https://webapp-leyu.com.cn',
    'keywords'    => ['乐鱼体育', '体育平台', '在线体育', '赛事直播'],
    'description' => '乐鱼体育提供全面的体育赛事信息与在线服务。',
    'lang'        => 'zh-CN',
];

$helper = new SiteMetaHelper($siteData);

echo "Short Description:\n";
echo $helper->generateDescription(120) . "\n\n";

echo "Full Meta Data:\n";
print_r($helper->getMeta());

echo "\nHTML Meta Tags:\n";
echo $helper->toHtmlMetaTags() . "\n";