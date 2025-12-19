import '../../styles/news.css'
import { Suspense } from 'react'
import { getCategories } from '@/lib/cms-api'
import CategoryFilter from '@/components/news/CategoryFilter'

export default async function NewsLayout({ children }) {
    // カテゴリ一覧を取得
    const categoriesData = await getCategories('news')
    const categories = categoriesData.success ? categoriesData.contents : []

    return (
        <>
            {/* メインビジュアル */}
            <div className="con_title">
                <div className="box_txt">
                    <h1>お知らせ</h1>
                </div>
                <p className="box_img">
                    <img
                        src="https://demo.abi-cms.net/hotel/news/images/title.jpg"
                        alt="お知らせ"
                    />
                </p>
            </div>

            {/* パンくず */}
            <ul
                className="topicpath"
                style={{ width: '90%', maxWidth: '1400px', margin: '1.5em auto 0' }}
            >
                <li property="itemListElement">
                    <a property="item" href="https://demo.abi-cms.net/hotel/">
                        <span property="name">Home</span>
                    </a>
                    <meta property="position" content="1" />
                </li>
                <li property="itemListElement">
                    <span property="name">お知らせ</span>
                    <meta property="position" content="2" />
                </li>
            </ul>

            <div
                className="wrap_news"
                style={{ width: '90%', maxWidth: '1400px', margin: '1.5em auto 0' }}
            >
                {children}

                <div className="con_side">
                    {/* カテゴリフィルター */}
                    <Suspense fallback={<div>Loading...</div>}>
                        <CategoryFilter categories={categories} />
                    </Suspense>
                </div>
            </div>
        </>
    )
}
