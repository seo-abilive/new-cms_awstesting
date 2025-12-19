'use client'

export default function TextLink({ block = {} }) {
    const { url, btn_title, is_blank = false } = block
    const target = is_blank ? '_blank' : '_self'

    return (
        <div className="box_news_parts">
            <p className="txt_link">
                <a href={url} target={target}>
                    {btn_title}
                </a>
            </p>
        </div>
    )
}
