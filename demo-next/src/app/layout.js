import '../styles/default.css'
import '../styles/common.css'
import '../styles/pagination.css'
import { Header } from '@/components/Header'
import Footer from '@/components/Footer'
import Body from '@/components/Body'
import 'slick-carousel/slick/slick.css'
import 'slick-carousel/slick/slick-theme.css'

export const metadata = {
    title: 'デモホテル',
    description: 'デモホテル',
}

export default function RootLayout({ children }) {
    return (
        <html lang="ja">
            <Body>
                <div id="abi_page">
                    <Header />
                    <main id="contents">{children}</main>
                    <Footer />
                </div>
            </Body>
        </html>
    )
}
