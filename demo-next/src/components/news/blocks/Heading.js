'use client'

export default function Heading({ block = {} }) {
    const { style, text } = block
    return (
        <>
            {style === '1' && (
                <div className="box_news_parts">
                    <h2 className="b_title">{text}</h2>
                </div>
            )}
            {style === '2' && (
                <div className="box_news_parts">
                    <h3 className="b_st">{text}</h3>
                </div>
            )}
            {style === '3' && (
                <div className="box_news_parts">
                    <h4 className="b_sst">{text}</h4>
                </div>
            )}
        </>
    )
}
