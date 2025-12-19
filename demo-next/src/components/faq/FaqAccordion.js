'use client'

import { useState } from 'react'

export default function FaqAccordion({ question, answer }) {
    const [isOpen, setIsOpen] = useState(false)
    const toggle = () => setIsOpen(!isOpen)

    return (
        <div className="faq_det">
            <p className={`accordion ${isOpen ? 'active' : ''}`} onClick={toggle}>
                <span>
                    <em>{question}</em>
                </span>
            </p>
            <div className="inner" style={{ display: isOpen ? 'block' : 'none' }}>
                <div className="answer" dangerouslySetInnerHTML={{ __html: answer }} />
            </div>
        </div>
    )
}
