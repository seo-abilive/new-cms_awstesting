import { getContentList, getCategories, getMarkupList } from '@/lib/cms-api'
import { getSearchParams } from '@/lib/utils'
import Pagination from '@/components/common/Pagination'
import MarkupScript from '@/components/common/MarkupScript'
import Link from 'next/link'

export const revalidate = 60

export async function generateMetadata({ searchParams }) {
    const params = await getSearchParams(searchParams)
    const title = params.category ? `お知らせ - ${params.category}` : 'お知らせ'
    return {
        title: params.page > 1 ? `${title} - ページ${params.page}` : title,
        description: 'お知らせ',
    }
}

export default async function News({ searchParams }) {
    const params = await getSearchParams(searchParams)
    let criteria = {}
    if (params.category) {
        criteria['category'] = params.category
    }

    // ニュース一覧を取得
    let newsList = { success: false, contents: [], all: 0, current: 1, pages: 1, limit: 10 }
    try {
        newsList = await getContentList('news', {
            limit: 10,
            page: params.page, // currentではなくpageを使用
            criteria: criteria,
        })
    } catch (error) {
        console.error('Failed to fetch news:', error)
    }

    // マークアップデータを取得
    let markupData = null
    try {
        const markupResponse = await getMarkupList('news', {
            limit: 10,
            offset: (params.page - 1) * 10,
            criteria: criteria,
        })
        if (markupResponse.success && markupResponse.contents) {
            markupData = markupResponse.contents
        }
    } catch (error) {
        console.error('Failed to fetch markup:', error)
    }

    const { contents, all, current, pages, limit } = newsList

    return (
        <>
            <div className="con_news p-news">
                {/* 新着情報 */}
                {contents.map((news, index) => {
                    const link = news.page_type === 'detail' ? `/news/${news.id}/` : news.page_url
                    const target = news.page_type === 'detail' ? '_self' : '_blank'
                    return (
                        <div className="box_news" key={index}>
                            <ul className="inn_news">
                                <li>
                                    <Link href={link} target={target}>
                                        <div className="wrp_txt">
                                            <div className="info">
                                                <p className="dat">{news.public_date}</p>
                                                {news.categories.length > 0 && (
                                                    <ul className="cat">
                                                        <li>
                                                            {news.categories
                                                                .map((category) => category.title)
                                                                .join(', ')}
                                                        </li>
                                                    </ul>
                                                )}
                                            </div>
                                            <p className="tit">{news.title}</p>
                                            <span className="i"></span>
                                        </div>
                                        {news.thumbnail && (
                                            <p className="photo">
                                                <img
                                                    src={news.thumbnail.file_url}
                                                    alt={news.title}
                                                />
                                            </p>
                                        )}
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    )
                })}

                {/* ページネーション */}
                <Pagination
                    currentPage={current}
                    totalPages={pages}
                    totalItems={all}
                    baseUrl="/news"
                    itemsPerPage={limit}
                    maxVisiblePages={3} // デフォルト3個表示
                    currentParams={{
                        category: params.category,
                    }}
                />
            </div>
            <MarkupScript markupData={markupData} />
        </>
    )
}
