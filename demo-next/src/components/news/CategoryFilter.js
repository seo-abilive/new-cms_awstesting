// components/news/CategoryFilter.js
'use client'

import Link from 'next/link'
import { useSearchParams } from 'next/navigation'

export default function CategoryFilter({ categories }) {
    const searchParams = useSearchParams()
    const currentCategory = searchParams.get('category')

    return (
        <div className="box_side box_cat">
            <p className="st accordion sp_only">
                <i>CATEGORY</i>
                <span>カテゴリ</span>
            </p>
            <ul>
                <li>
                    <Link href="/news" className={!currentCategory ? 'active' : ''}>
                        すべての記事
                    </Link>
                </li>
                {categories.map((category) => (
                    <li key={category.id}>
                        <Link
                            href={`/news?category=${category.id}`}
                            className={currentCategory === category.id.toString() ? 'active' : ''}
                        >
                            {category.title} ({category.contents_count || 0})
                        </Link>
                    </li>
                ))}
            </ul>
        </div>
    )
}
