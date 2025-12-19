import Link from 'next/link'
import '../../styles/faq.css'
import FaqAccordion from '@/components/faq/FaqAccordion'
import MarkupScript from '@/components/common/MarkupScript'
import { getCategories, getContentList, getMarkupList } from '@/lib/cms-api'

export function generateMetadata() {
    return {
        title: 'よくあるご質問',
        description: 'よくあるご質問',
    }
}

export default async function Faq() {
    // カテゴリ取得
    let categories = { success: false, contents: [] }
    try {
        categories = await getCategories('faq')
    } catch (error) {
        console.error('Failed to fetch categories:', error)
    }

    // FAQ取得
    let faqs = { success: false, contents: [] }
    try {
        faqs = await getContentList('faq', { mode: 'all' })
    } catch (error) {
        console.error('Failed to fetch faqs:', error)
    }

    // マークアップデータを取得
    let markupData = null
    try {
        const markupResponse = await getMarkupList('faq', { mode: 'all' })
        if (markupResponse.success && markupResponse.contents) {
            markupData = markupResponse.contents
        }
    } catch (error) {
        console.error('Failed to fetch markup:', error)
    }

    // カテゴリとFAQを結合（データが取得できた場合のみ）
    const faqList =
        categories.success && faqs.success
            ? categories.contents.map((category) => {
                  return {
                      category: category,
                      faqs: faqs.contents.filter((faq) =>
                          faq.categories.some((cat) => cat.id === category.id)
                      ),
                  }
              })
            : []

    return (
        <>
            <div className="con_title">
                <div className="box_txt">
                    <h1>よくあるご質問</h1>
                </div>
                <p className="box_img">
                    <img
                        src="https://demo.abi-cms.net/hotel/faq/images/title.jpg"
                        alt="よくあるご質問"
                    />
                </p>
            </div>

            {/* パンくず */}
            <ul
                className="topicpath"
                style={{ width: '90%', maxWidth: '1400px', margin: '1.5em auto 0' }}
            >
                <li property="itemListElement">
                    <Link href="/" property="item">
                        <span property="name">Home</span>
                    </Link>
                    <meta property="position" content="1" />
                </li>
                <li property="itemListElement">
                    <span property="name">よくあるご質問</span>
                    <meta property="position" content="2" />
                </li>
            </ul>

            {/* タブ */}
            <div
                className="con_tab"
                style={{ width: '90%', maxWidth: '1400px', margin: '1.5em auto 0' }}
            >
                <div className="area">
                    <ul>
                        {faqList.map((faq) => (
                            <li className="c-btn1 c-btn1-wht" key={faq.category.id}>
                                <a href={`#link${faq.category.id}`}>{faq.category.title}</a>
                            </li>
                        ))}
                    </ul>
                </div>
            </div>

            {/* コンテンツ */}
            <div className="con_faq">
                {faqList.map((faq) => (
                    <div className="box_faq" id={`link${faq.category.id}`} key={faq.category.id}>
                        <h3>
                            <span>{faq.category.title}</span>
                        </h3>
                        {faq.faqs.map((faq) => (
                            <FaqAccordion
                                key={faq.id}
                                question={faq.question}
                                answer={faq.answer}
                            />
                        ))}
                    </div>
                ))}
            </div>
            <MarkupScript markupData={markupData} />
        </>
    )
}
