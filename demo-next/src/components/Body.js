'use client'

import { usePathname } from 'next/navigation'
import { useEffect } from 'react'

export default function Body({ children }) {
    const pathname = usePathname()

    const bodyId = (() => {
        if (pathname === '/') return 'home'
        if (pathname.startsWith('/faq')) return 'faq'
        if (pathname.startsWith('/news')) return 'news'
        return 'default'
    })()

    useEffect(() => {
        document.body.id = bodyId
    }, [bodyId])

    return <body>{children}</body>
}
