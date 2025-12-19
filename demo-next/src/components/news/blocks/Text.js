'use client'

export default function Text({ block = '' }) {
    return (
        <div className="box_news_parts">
            <div className="b_txt" dangerouslySetInnerHTML={{ __html: block }} />
        </div>
    )
}
