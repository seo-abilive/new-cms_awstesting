'use client'

export default function Images({ block = [] }) {
    const imageNum = block.length

    return (
        <>
            <div className="box_news_parts">
                <ul className={`b_img col_${String(imageNum).padStart(2, '0')}`}>
                    {block.map((image, index) => (
                        <li key={index}>
                            <p className="photo">
                                <img src={image.file_url} alt="" />
                            </p>
                        </li>
                    ))}
                </ul>
            </div>
        </>
    )
}
