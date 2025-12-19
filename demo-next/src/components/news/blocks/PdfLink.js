'use client'

export default function PdfLink({ block = {} }) {
    const { file, button_title } = block
    const fileUrl = file.file_url

    return (
        <div className="box_news_parts">
            <p className="pdf_link">
                <a href={fileUrl} target="_blank">
                    {button_title}
                </a>
            </p>
        </div>
    )
}
