import Link from 'next/link'
import '../../styles/contact.css'

export function generateMetadata() {
    return {
        title: 'お問い合わせ',
        description: 'お問い合わせ',
    }
}

export default function ContactPage() {
    return (
        <>
            {/* メインビジュアル */}
            <div class="con_title">
                <div class="box_txt">
                    <h1>お問い合わせ</h1>
                </div>
                <p class="box_img">
                    <img
                        src="https://demo.abi-cms.net/hotel/contact/images/title.jpg"
                        alt="お問い合わせ"
                    />
                </p>
            </div>

            {/* パンくず */}
            <ul
                class="topicpath"
                vocab="https://schema.org/"
                typeof="BreadcrumbList"
                style={{ width: '90%', maxWidth: '1400px', margin: '1.5em auto 0' }}
            >
                <li property="itemListElement" typeof="ListItem">
                    <Link href="/" property="item">
                        <span property="name">Home</span>
                    </Link>
                    <meta property="position" content="1" />
                </li>
                <li property="itemListElement" typeof="ListItem">
                    <span property="name">お問い合わせ</span>
                    <meta property="position" content="2" />
                </li>
            </ul>

            <div
                class="con_form"
                style={{ width: '90%', maxWidth: '1400px', margin: '1.5em auto 0' }}
            >
                <div class="box_int">
                    <p class="txt">
                        よくいただくお問い合わせ内容をおまとめしております。
                        <br />
                        事前にご確認いただければ、解決策が見つかるかもしれません。ぜひご活用くださいませ。
                    </p>
                    <p class="btn c-btn1">
                        <Link href="/faq">よくあるご質問</Link>
                    </p>
                </div>

                <ul class="box_note">
                    <li>
                        ・お問い合わせ内容の確認後、担当者よりご回答をさせていただきます。（土・日・祝・年末年始を除く）
                        <br />
                        なお、ご回答までに多少の時間を要する場合がございますので、あらかじめご了承ください。お急ぎの場合は、ホテルにお電話にてお問い合わせください。
                    </li>
                    <li>・メールアドレスが正しくない場合は、ご返信ができません。</li>
                    <li>
                        ・お問い合わせ内容によりましてはご返信ができない場合もございますので、ご了承ください。
                    </li>
                    <li>・ホテルにご宿泊のお客様宛のご伝言にはご利用になれません。</li>
                    <li>
                        ・ご入力いただきました個人情報はお問い合わせに対するご回答にのみ使用させていただきます。他の目的には一切使用いたしません。
                    </li>
                </ul>

                <div class="panel">
                    <div class="box_panel" id="panel02">
                        <div class="box_form">
                            <p class="txt_attention">
                                <i>※</i>{' '}
                                印は必須項目です。必ずご記入ください。（ご予約、キャンセル、お急ぎのご用件は各ホテルにご連絡ください。）
                            </p>
                            <iframe
                                src="http://localhost:5173/widget/contact/ac61618e-b747-4e0b-916b-a8f5cabdb0ee"
                                style={{ border: 'none', width: '100%', height: '600px' }}
                            ></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}
