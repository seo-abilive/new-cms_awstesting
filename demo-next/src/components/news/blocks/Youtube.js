'use client'

export default function Youtube({ block }) {
    const { url, description } = block

    let youtubeId = null
    // サポート: https://youtu.be/xxxxxxx 形式のURLもある場合のためID取得ロジックを強化
    let youtubeIdMatch = null
    if (url.includes('youtu.be/')) {
        youtubeIdMatch = url.match(/youtu\.be\/([^?&]+)/)
        if (youtubeIdMatch) {
            youtubeId = youtubeIdMatch[1]
        }
    } else if (url.includes('youtube.com')) {
        // v=ID形式・またはembed/ID形式
        youtubeIdMatch = url.match(/[?&]v=([^?&]+)/)
        if (youtubeIdMatch) {
            youtubeId = youtubeIdMatch[1]
        } else {
            youtubeIdMatch = url.match(/embed\/([^?&]+)/)
            if (youtubeIdMatch) {
                youtubeId = youtubeIdMatch[1]
            }
        }
    }
    const src = `https://www.youtube.com/embed/${youtubeId}`

    return (
        <div className="box_news_parts">
            <div className="b_youtube_l">
                <div className="iframe_res">
                    {youtubeId && (
                        <iframe
                            width="1200"
                            height="679"
                            src={src}
                            frameBorder="0"
                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                            allowFullScreen
                        ></iframe>
                    )}
                </div>
                {description && <p className="txt_caption">{description}</p>}
            </div>
        </div>
    )
}
