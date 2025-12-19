'use client'

export default function ImageText({ block = {} }) {
    const { image, text, layout = 'left' } = block

    const classNames = layout === 'left' ? 'img_l_txt' : 'img_r_txt'
    const imgClassNames = layout === 'left' ? 'box_img' : 'photo'

    return (
        <>
            {/* 左画像+右テキスト */}
            <div className="box_news_parts">
                <div className={classNames}>
                    {image && (
                        <p className={imgClassNames}>
                            <img src={image.file_url} alt="" />
                        </p>
                    )}
                    {text && <div className="txt" dangerouslySetInnerHTML={{ __html: text }} />}
                </div>
            </div>
        </>
    )
}
