<?php
namespace Core;

/**
 * ページネーション生成クラス
 * APIから返されるページネーション情報を基に、ページネーション配列を生成
 */
class Pagination
{
    private int $total;
    private int $current;
    private int $pages;
    private int $limit;
    private int $maxVisiblePages;
    private string $baseUrl;
    private array $queryParams;

    /**
     * コンストラクタ
     *
     * @param array $paginationData APIから返されるページネーション情報
     * @param string $baseUrl ベースURL
     * @param array $queryParams クエリパラメータ
     * @param int $maxVisiblePages 表示する最大ページ数
     */
    public function __construct(
        array $paginationData,
        string $baseUrl = '',
        array $queryParams = [],
        int $maxVisiblePages = 5
    ) {
        $this->total = $paginationData['total'] ?? 0;
        $this->current = $paginationData['current'] ?? 1;
        $this->pages = $paginationData['pages'] ?? 1;
        $this->limit = $paginationData['limit'] ?? 10;
        $this->maxVisiblePages = $maxVisiblePages;
        $this->baseUrl = $baseUrl;
        $this->queryParams = $queryParams;
    }

    /**
     * ページネーション配列を生成
     *
     * @return array
     */
    public function generate(): array
    {
        $hasPrev = $this->current > 1;
        $hasNext = $this->current < $this->pages;
        $prevPage = $hasPrev ? $this->current - 1 : null;
        $nextPage = $hasNext ? $this->current + 1 : null;

        $pagination = [
            'total' => $this->total,
            'current' => $this->current,
            'pages' => $this->pages,
            'limit' => $this->limit,
            'has_prev' => $hasPrev,
            'has_next' => $hasNext,
            'prev_page' => $prevPage,
            'next_page' => $nextPage,
            'prev_url' => $prevPage ? $this->createUrl($prevPage) : null,
            'next_url' => $nextPage ? $this->createUrl($nextPage) : null,
            'page_numbers' => $this->generatePageNumbers(),
            'first_page' => $this->generateFirstPage(),
            'last_page' => $this->generateLastPage(),
            'prev_ellipsis' => $this->generatePrevEllipsis(),
            'next_ellipsis' => $this->generateNextEllipsis(),
        ];

        return $pagination;
    }

    /**
     * 表示するページ番号の配列を生成
     *
     * @return array
     */
    private function generatePageNumbers(): array
    {
        $pageNumbers = [];
        
        if ($this->pages <= $this->maxVisiblePages) {
            // 全ページを表示
            for ($i = 1; $i <= $this->pages; $i++) {
                $pageNumbers[] = $this->createPageData($i);
            }
        } else {
            // 現在のページを中心に表示
            $start = max(1, $this->current - floor($this->maxVisiblePages / 2));
            $end = min($this->pages, $start + $this->maxVisiblePages - 1);
            
            // 開始位置を調整
            if ($end - $start + 1 < $this->maxVisiblePages) {
                $start = max(1, $end - $this->maxVisiblePages + 1);
            }
            
            for ($i = $start; $i <= $end; $i++) {
                $pageNumbers[] = $this->createPageData($i);
            }
        }

        return $pageNumbers;
    }

    /**
     * 最初のページ情報を生成
     *
     * @return array|null
     */
    private function generateFirstPage(): ?array
    {
        if ($this->current > 2 && $this->pages > $this->maxVisiblePages) {
            return $this->createPageData(1);
        }
        return null;
    }

    /**
     * 最後のページ情報を生成
     *
     * @return array|null
     */
    private function generateLastPage(): ?array
    {
        if ($this->current < $this->pages - 1 && $this->pages > $this->maxVisiblePages) {
            return $this->createPageData($this->pages);
        }
        return null;
    }

    /**
     * 前の省略記号を生成
     *
     * @return bool
     */
    private function generatePrevEllipsis(): bool
    {
        return $this->current > 3 && $this->pages > $this->maxVisiblePages;
    }

    /**
     * 次の省略記号を生成
     *
     * @return bool
     */
    private function generateNextEllipsis(): bool
    {
        return $this->current < $this->pages - 2 && $this->pages > $this->maxVisiblePages;
    }

    /**
     * ページデータを作成
     *
     * @param int $pageNumber
     * @return array
     */
    private function createPageData(int $pageNumber): array
    {
        $url = $this->createUrl($pageNumber);

        return [
            'number' => $pageNumber,
            'url' => $url,
            'is_current' => $pageNumber === $this->current,
            'is_active' => true,
        ];
    }

    /**
     * 指定ページのURLを生成
     */
    private function createUrl(int $pageNumber): string
    {
        $queryParams = array_merge($this->queryParams, ['page' => $pageNumber]);
        $query = http_build_query($queryParams);
        // 既にクエリを含むbaseUrlにも対応
        if (strpos($this->baseUrl, '?') !== false) {
            return rtrim($this->baseUrl, '&') . '&' . $query;
        }
        return $this->baseUrl . '?' . $query;
    }

    /**
     * ページネーション情報を文字列で取得
     *
     * @return string
     */
    public function getInfo(): string
    {
        $start = ($this->current - 1) * $this->limit + 1;
        $end = min($this->current * $this->limit, $this->total);
        
        return "{$start}-{$end} / {$this->total}件";
    }

    /**
     * ページネーション情報を配列で取得
     *
     * @return array
     */
    public function getInfoArray(): array
    {
        $start = ($this->current - 1) * $this->limit + 1;
        $end = min($this->current * $this->limit, $this->total);
        
        return [
            'start' => $start,
            'end' => $end,
            'total' => $this->total,
            'current_page' => $this->current,
            'total_pages' => $this->pages,
            'per_page' => $this->limit,
        ];
    }

    /**
     * ベースURLを設定
     *
     * @param string $baseUrl
     * @return self
     */
    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * クエリパラメータを設定
     *
     * @param array $queryParams
     * @return self
     */
    public function setQueryParams(array $queryParams): self
    {
        $this->queryParams = $queryParams;
        return $this;
    }

    /**
     * 最大表示ページ数を設定
     *
     * @param int $maxVisiblePages
     * @return self
     */
    public function setMaxVisiblePages(int $maxVisiblePages): self
    {
        $this->maxVisiblePages = $maxVisiblePages;
        return $this;
    }
}
