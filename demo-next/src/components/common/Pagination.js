// components/common/Pagination.js
import Link from 'next/link'
import { buildPaginationUrl } from '@/lib/utils'

export default function Pagination({
    currentPage,
    totalPages,
    totalItems,
    baseUrl = '/news',
    itemsPerPage = 10,
    currentParams = {}, // 現在のURLパラメータ（カテゴリなど）
    maxVisiblePages = 3, // 表示する最大ページ数（デフォルト3個）
}) {
    // ページネーションの表示ロジック
    const getPageNumbers = () => {
        const pages = []

        if (totalPages <= maxVisiblePages) {
            // 総ページ数が少ない場合は全て表示
            for (let i = 1; i <= totalPages; i++) {
                pages.push(i)
            }
        } else {
            // 現在のページを中心に表示
            const start = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2))
            const end = Math.min(totalPages, start + maxVisiblePages - 1)

            // 最後のページが表示範囲に来るように調整
            const adjustedStart = Math.max(1, end - maxVisiblePages + 1)

            for (let i = adjustedStart; i <= end; i++) {
                pages.push(i)
            }
        }

        return pages
    }

    const pageNumbers = getPageNumbers()
    const hasPrevious = currentPage > 1
    const hasNext = currentPage < totalPages

    if (totalPages <= 1) {
        return null // 1ページのみの場合は表示しない
    }

    return (
        <div className="box_pager">
            <ul className="pagination-list">
                {/* 前へボタン */}
                <li className={`pagination-item ${!hasPrevious ? 'disabled' : ''}`}>
                    {hasPrevious ? (
                        <Link
                            href={buildPaginationUrl(baseUrl, currentParams, currentPage - 1)}
                            className="pagination-link"
                        >
                            <span className="r_arrow">前へ</span>
                        </Link>
                    ) : (
                        <span className="pagination-link disabled">
                            <span className="r_arrow">前へ</span>
                        </span>
                    )}
                </li>

                {/* ページ番号 */}
                {pageNumbers.map((pageNum) => (
                    <li
                        key={pageNum}
                        className={`pagination-item ${pageNum === currentPage ? 'active' : ''}`}
                    >
                        <Link
                            href={buildPaginationUrl(baseUrl, currentParams, pageNum)}
                            className="pagination-link"
                        >
                            {pageNum}
                        </Link>
                    </li>
                ))}

                {/* 次へボタン */}
                <li className={`pagination-item ${!hasNext ? 'disabled' : ''}`}>
                    {hasNext ? (
                        <Link
                            href={buildPaginationUrl(baseUrl, currentParams, currentPage + 1)}
                            className="pagination-link"
                        >
                            <span className="r_arrow">次へ</span>
                        </Link>
                    ) : (
                        <span className="pagination-link disabled">
                            <span className="r_arrow">次へ</span>
                        </span>
                    )}
                </li>
            </ul>
        </div>
    )
}
