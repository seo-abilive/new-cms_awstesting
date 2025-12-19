'use client'

export default function ButtonLink({ block = {} }) {
    const { url, button_title, color = 'blue' } = block
    const colorClass = color === 'blue' ? 'c-btn1' : 'c-btn1-rsv'

    return (
        <>
            <div className="box_news_parts">
                <p className={`btn ${colorClass}`}>
                    <a href={url} target="_blank">
                        {button_title}
                    </a>
                </p>
            </div>
        </>
    )
}
