import { getContentDetail, getContentList, getMarkupDetail } from '@/lib/cms-api'
import { notFound } from 'next/navigation'
import MarkupScript from '@/components/common/MarkupScript'
import Link from 'next/link'
import ButtonLink from '@/components/news/blocks/ButtonLink'
import Heading from '@/components/news/blocks/Heading'
import Images from '@/components/news/blocks/Images'
import ImageSlide from '@/components/news/blocks/ImageSlide'
import ImageText from '@/components/news/blocks/ImageText'
import PdfLink from '@/components/news/blocks/PdfLink'
import Table from '@/components/news/blocks/Table'
import Text from '@/components/news/blocks/Text'
import TextLink from '@/components/news/blocks/TextLink'
import Youtube from '@/components/news/blocks/Youtube'

export async function generateStaticParams() {
    const list = await getContentList('news', { mode: 'all' })
    if (!list?.success || !Array.isArray(list.contents)) return []
    return list.contents.map((item) => ({ id: item.id.toString() }))
}

// データ取得関数を分離
async function getNewsDetail(id) {
    return await getContentDetail('news', id, {
        criteria: { page_type: 'detail' },
    })
}

export async function generateMetadata({ params }) {
    const resolvedParams = await params
    const data = await getNewsDetail(resolvedParams.id)

    if (!data.success) {
        return {
            title: '記事が見つかりません',
        }
    }

    return {
        title: data.contents.title,
        description: data.contents.excerpt,
        openGraph: {
            title: data.contents.title,
            description: data.contents.excerpt,
        },
    }
}

export default async function NewsDetail({ params }) {
    const resolvedParams = await params
    const { id } = resolvedParams

    // 記事詳細を取得
    const data = await getNewsDetail(id)

    // マークアップデータを取得
    let markupData = null
    try {
        const markupResponse = await getMarkupDetail('news', id)
        if (markupResponse.success && markupResponse.contents) {
            markupData = markupResponse.contents
        }
    } catch (error) {
        console.error('Failed to fetch markup:', error)
    }

    if (!data.success) {
        console.log('NewsDetail - API Error:', data.error)
        return (
            <div className="con_news">
                <div className="box_detail">
                    <div className="wrp_inf">
                        <h1 className="wrp_tit">記事が見つかりません</h1>
                        <p>エラー: {data.error}</p>
                        <p>ID: {id}</p>
                        <Link href="/news">記事一覧に戻る</Link>
                    </div>
                </div>
            </div>
        )
    }

    const { contents, sibLings } = data

    return (
        <>
            <div className="con_news">
                <div className="box_detail">
                    <div className="wrp_inf">
                        <p className="dat">{contents.public_date}</p>
                        <h1 className="wrp_tit">{contents.title}</h1>

                        {/* ブロックコンテンツ */}
                        <div className="wrp_det">
                            {contents.custom_block &&
                                contents.custom_block.map((block, index) => {
                                    const blockType = Object.keys(block)[0]
                                    const blockData = block[blockType]

                                    switch (blockType) {
                                        case 'text':
                                            return <Text key={index} block={blockData} />
                                        case 'heading':
                                            return <Heading key={index} block={blockData} />
                                        case 'images':
                                            return <Images key={index} block={blockData} />
                                        case 'slide_images':
                                            return <ImageSlide key={index} block={blockData} />
                                        case 'image_text':
                                            return <ImageText key={index} block={blockData} />
                                        case 'youtube':
                                            return <Youtube key={index} block={blockData} />
                                        case 'table':
                                            return <Table key={index} block={blockData} />
                                        case 'link_button':
                                            return <ButtonLink key={index} block={blockData} />
                                        case 'text_link':
                                            return <TextLink key={index} block={blockData} />
                                        case 'pdf_button':
                                            return <PdfLink key={index} block={blockData} />
                                        default:
                                            return null
                                    }
                                })}
                        </div>
                    </div>
                </div>

                {/* 前、次の記事 */}
                <div className="box_arrow">
                    {sibLings?.previous && (
                        <p className="prev slick-arrow">
                            <Link href={`/news/${sibLings.previous.id}`}>
                                <i className="ic-chevron-left"></i>PREV
                            </Link>
                        </p>
                    )}

                    <p className="btn">
                        <Link href="/news">
                            記事一覧<span className="view_pc-tab">に戻る</span>
                        </Link>
                    </p>

                    {sibLings?.next && (
                        <p className="next slick-arrow">
                            <Link href={`/news/${sibLings.next.id}`}>
                                NEXT<i className="ic-chevron-right"></i>
                            </Link>
                        </p>
                    )}
                </div>
            </div>
            <MarkupScript markupData={markupData} />
        </>
    )
}
