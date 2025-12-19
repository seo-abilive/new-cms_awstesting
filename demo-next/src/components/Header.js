import Link from 'next/link'

export const Header = () => {
    return (
        <header id="header">
            <div className="con_header">
                <p className="logo">
                    <Link href="/">
                        <img
                            className="icon"
                            src="https://demo.abi-cms.net/hotel/files/images/common/logo-151x32.png"
                            alt=""
                        />
                    </Link>
                </p>
                <nav className="box_nav view_pc-tab">
                    <ul id="gnav">
                        <li>
                            <Link href="/">HOME</Link>
                        </li>
                        <li>
                            <Link href="/news/">新着情報</Link>
                        </li>
                        <li>
                            <Link href="/faq/">よくあるご質問</Link>
                        </li>
                        <li>
                            <Link href="/contact/">お問い合わせ</Link>
                        </li>
                    </ul>
                    <div className="rsv">
                        <Link
                            href="https://www.489pro-x.com/ja/s/ablivehotel/search/"
                            target="_blank"
                            rel="noopener"
                        >
                            <span>空室検索</span>
                        </Link>
                    </div>
                    <div className="menu js-btn_menu">
                        <span className="c-en_capb">MENU</span>
                    </div>
                </nav>
            </div>
        </header>
    )
}
