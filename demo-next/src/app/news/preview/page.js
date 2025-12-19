import { cookies } from 'next/headers'
import { getMarkupList } from '@/lib/cms-api'
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

// プレビューデータ取得
async function getNewsPreview() {
    try {
        const cookieStore = await cookies()
        const previewData = cookieStore.get('preview_data')

        if (previewData?.value) {
            const data = JSON.parse(previewData.value)
            console.log('Preview data loaded from cookie:', {
                hasTitle: !!data.title,
                hasPublicDate: !!data.public_date,
                hasCustomBlock: !!data.custom_block,
                customBlockLength: data.custom_block?.length || 0,
            })
            return data
        } else {
            console.log('No preview data in cookie')
        }
    } catch (error) {
        console.error('Failed to parse preview data:', error)
    }

    // デフォルトデータ（データがない場合）
    return null
}

export async function generateMetadata({ params }) {
    const resolvedParams = await params
    const contents = await getNewsPreview()

    if (!contents) {
        return {
            title: 'プレビュー',
            description: 'プレビュー',
        }
    }

    return {
        title: contents.title || 'プレビュー',
        description: contents.excerpt || contents.title || 'プレビュー',
        openGraph: {
            title: contents.title || 'プレビュー',
            description: contents.excerpt || contents.title || 'プレビュー',
        },
    }
}

export default async function NewsPreview({ params }) {
    const resolvedParams = await params
    const contents = await getNewsPreview()

    // マークアップデータを取得（プレビュー用）
    let markupData = null
    try {
        const markupResponse = await getMarkupList('news')
        if (markupResponse.success && markupResponse.contents) {
            markupData = markupResponse.contents
        }
    } catch (error) {
        console.error('Failed to fetch markup:', error)
    }

    // プレビューデータがない場合
    if (!contents) {
        return (
            <div className="con_news">
                <div className="box_detail">
                    <div className="wrp_inf">
                        <h1 className="wrp_tit">プレビューデータがありません</h1>
                        <p>プレビューデータを送信してください。</p>
                        <Link href="/news">記事一覧に戻る</Link>
                    </div>
                </div>
            </div>
        )
    }

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

                {/* 記事一覧に戻るボタン */}
                <div className="box_arrow">
                    <p className="btn">
                        <Link href="/news">
                            記事一覧<span className="view_pc-tab">に戻る</span>
                        </Link>
                    </p>
                </div>
            </div>
            <MarkupScript markupData={markupData} />
        </>
    )
}
