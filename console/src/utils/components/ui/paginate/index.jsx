import React from 'react'
import { styled } from 'styled-components'
import { Button, ButtonGroup } from '@/utils/components/ui/button'

export const Paginate = ({ currentPage = 1, totalPages = 1, onPageChange = () => {} }) => {
    const Wrapper = styled.div``

    // ページ番号リストを作成
    const getPageNumbers = () => {
        const pages = []
        const maxVisible = 5 // 表示するページ数
        let start = Math.max(1, currentPage - 2)
        let end = Math.min(totalPages, start + maxVisible - 1)

        // ページが少ない場合
        if (end - start < maxVisible - 1) {
            start = Math.max(1, end - maxVisible + 1)
        }

        for (let i = start; i <= end; i++) {
            pages.push(i)
        }
        return pages
    }

    return (
        <Wrapper>
            {/* 最初のページ */}
            <ButtonGroup className="shadow-none">
                <Button
                    color={'light'}
                    className="page-btn text-sm text-gray-400 dark:text-gray-400 border-1"
                    onClick={() => onPageChange(1)}
                    disabled={currentPage === 1}
                    aria-label="Go to first page"
                    size="sm"
                    outline
                >
                    «
                </Button>

                {/* 前のページ */}
                <Button
                    color={'light'}
                    className="page-btn text-sm text-gray-400 dark:text-gray-400 border-1"
                    onClick={() => onPageChange(currentPage - 1)}
                    disabled={currentPage === 1}
                    aria-label="Previous page"
                    size="sm"
                    outline
                >
                    ‹
                </Button>

                {/* 中央のページ番号 */}
                {getPageNumbers().map((page) => (
                    <Button
                        color={'light'}
                        key={page}
                        className={`page-btn text-sm text-gray-400 dark:text-gray-400  border-1 ${
                            page === currentPage ? 'active' : ''
                        }`}
                        onClick={() => onPageChange(page)}
                        disabled={page === currentPage}
                        aria-label={`Go to page ${page}`}
                        size="sm"
                        outline
                    >
                        {page}
                    </Button>
                ))}

                {/* 次のページ */}
                <Button
                    color={'light'}
                    className="page-btn text-sm text-gray-400 dark:text-gray-400 border-1"
                    onClick={() => onPageChange(currentPage + 1)}
                    disabled={currentPage === totalPages}
                    aria-label="Next page"
                    size="sm"
                    outline
                >
                    ›
                </Button>

                {/* 最後のページ */}
                <Button
                    color={'light'}
                    className="page-btn text-sm text-gray-400 dark:text-gray-400 border-1"
                    onClick={() => onPageChange(totalPages)}
                    disabled={currentPage === totalPages}
                    aria-label="Go to last page"
                    size="sm"
                    outline
                >
                    »
                </Button>
            </ButtonGroup>
        </Wrapper>
    )
}
