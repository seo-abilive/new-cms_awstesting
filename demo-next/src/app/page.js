import Slider from '@/components/Slider'
import '../styles/homepage.css'
import Link from 'next/link'
import { getContentList } from '@/lib/cms-api'

export default async function Home() {
    // 新着情報を取得
    let newsList = { success: false, contents: [] }
    try {
        newsList = await getContentList('news', { limit: 3 })
    } catch (error) {
        console.error('Failed to fetch news:', error)
    }

    // バナーを取得
    let bannerList = { success: false, contents: [] }
    try {
        bannerList = await getContentList('top_banner', { mode: 'all' })
    } catch (error) {
        console.error('Failed to fetch banners:', error)
    }

    // MV スライダー設定
    const mvSettings = {
        dots: true,
        arrows: false,
        infinite: true,
        fade: true, // ← フェード
        speed: 1000, // ← フェード速度
        autoplay: true,
        autoplaySpeed: 5000, // ← 次の画像までの時間
        pauseOnHover: false, // ← ホバーで止めない
        cssEase: 'linear',
    }

    // バナースライダー設定
    const bannerSettings = {
        dots: true,
        arrows: true,
        infinite: true,
        slidesToShow: 2, // 画面に表示する数
        slidesToScroll: 2,
        speed: 1000,
        autoplay: true,
        autoplaySpeed: 3000,
        pauseOnHover: true,
        centerMode: false, // ← 中央寄せを無効化
        centerPadding: '0px', // 両端の余白
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    centerMode: false,
                    centerPadding: '0px',
                },
            },
        ],
    }

    return (
        <>
            {/* <!-- MV --> */}
            <div className="con_mainimg">
                <Slider settings={mvSettings} className="box_img">
                    <div className="slide">
                        <p className="photo">
                            <img
                                src="https://demo.abi-cms.net/hotel/files/images/home/img_mv01.png"
                                alt="demo"
                            />
                        </p>
                        <div className="box_txt"></div>
                    </div>
                    <div className="slide">
                        <p className="photo">
                            <img
                                src="https://demo.abi-cms.net/hotel/files/images/home/img_mv02.png"
                                alt="demo"
                            />
                        </p>
                        <div className="box_txt"></div>
                    </div>
                    <div className="slide">
                        <p className="photo">
                            <img
                                src="https://demo.abi-cms.net/hotel/files/images/home/img_mv03.png"
                                alt="demo"
                            />
                        </p>
                        <div className="box_txt"></div>
                    </div>
                </Slider>
            </div>

            <div className="wrp_pickup">
                {bannerList.success && bannerList.contents.length > 0 && (
                    <div className="con_pickup">
                        <Slider {...bannerSettings} className="box_img" domId="js-picSlider">
                            {bannerList.contents.map((banner) => (
                                <div className="slide" key={banner.id}>
                                    {banner.url ? (
                                        <a
                                            href={banner.url}
                                            target={banner.is_blank ? '_blank' : '_self'}
                                        >
                                            <img
                                                src={banner.image.file_url}
                                                alt={banner.image.alt_text}
                                            />
                                        </a>
                                    ) : (
                                        <img
                                            src={banner.image.file_url}
                                            alt={banner.image.alt_text}
                                        />
                                    )}
                                </div>
                            ))}
                        </Slider>
                    </div>
                )}

                {/* <!-- 新着情報 --> */}
                <div className="wrp_news">
                    <div className="con_news p-news" style={{ margin: '0 auto' }}>
                        <h2 className="st c-st1">
                            <i>NEWS</i>
                            <span>お知らせ</span>
                        </h2>
                        <div className="box_news" style={{ marginTop: '40px' }}>
                            {newsList.success && newsList.contents.length > 0 && (
                                <ul className="inn_news">
                                    {newsList.contents.map((news) => {
                                        const link =
                                            news.page_type === 'detail'
                                                ? `/news/${news.id}/`
                                                : news.page_url
                                        const target =
                                            news.page_type === 'detail' ? '_self' : '_blank'
                                        return (
                                            <li key={news.id}>
                                                <a href={link} target={target}>
                                                    <div className="wrp_txt">
                                                        <div className="info">
                                                            <p className="dat">
                                                                {news.public_date}
                                                            </p>
                                                            {news.categories.length > 0 && (
                                                                <ul className="cat">
                                                                    {news.categories.map(
                                                                        (category) => (
                                                                            <li key={category.id}>
                                                                                {category.title}
                                                                            </li>
                                                                        )
                                                                    )}
                                                                </ul>
                                                            )}
                                                        </div>
                                                        <p className="tit">{news.title}</p>
                                                        <span className="i"></span>
                                                    </div>
                                                    {news.thumbnail && (
                                                        <p className="photo">
                                                            <img
                                                                src={news.thumbnail.file_url}
                                                                alt={news.title}
                                                            />
                                                        </p>
                                                    )}
                                                </a>
                                            </li>
                                        )
                                    })}
                                </ul>
                            )}
                        </div>
                        <p className="btn c-btn1">
                            <Link href="/news/">一覧を見る</Link>
                        </p>
                    </div>
                </div>

                {/* <!-- /6つの魅力 --> */}
                <div className="con_feat">
                    <div className="box_photo">
                        <h2 className="c-st1">
                            <i>FEATURES</i>
                            <span>6つの魅力</span>
                        </h2>
                        <div className="box_txt">
                            <p className="st c-jp_h3">
                                快適に安心してお過ごしいただきたい。
                                <br />
                                その想いで皆様をお待ちしております。
                            </p>
                        </div>
                    </div>
                    <ul className="box_feat">
                        <li>
                            <div className="photo">
                                <img
                                    src="https://demo.abi-cms.net/hotel/files/images/home/img_features01.png"
                                    alt="LADIES"
                                />
                                <p className="ic">
                                    <em className="num">01．</em>
                                    <span className="t">LADIES</span>
                                </p>
                            </div>
                            <div className="box_txt">
                                <p className="txt c-jp_b1">
                                    6Fはワンフロア女性専用。女性の一人旅でも安心してご滞在いただけます。
                                </p>
                            </div>
                        </li>
                        <li>
                            <div className="photo">
                                <img
                                    src="https://demo.abi-cms.net/hotel/files/images/home/img_features02.png"
                                    alt="STAY"
                                />
                                <p className="ic">
                                    <em className="num">02．</em>
                                    <span className="t">STAY</span>
                                </p>
                            </div>
                            <div className="box_txt">
                                <p className="txt c-jp_b1">
                                    こだわりのホテルオリジナル寝具を全室に採用。くつろぎのひとときをお過ごしください。
                                </p>
                            </div>
                        </li>
                        <li>
                            <div className="photo">
                                <img
                                    src="https://demo.abi-cms.net/hotel/files/images/home/img_features03.png"
                                    alt="TABLET"
                                />
                                <p className="ic">
                                    <em className="num">03．</em>
                                    <span className="t">TABLET</span>
                                </p>
                            </div>
                            <div className="box_txt">
                                <p className="txt c-jp_b1">
                                    ホテル専用タブレットを全室に設置。館内施設の情報やお食事会場の混雑状況、おすすめの観光スポットなどご覧いただけます。
                                </p>
                            </div>
                        </li>
                        <li>
                            <div className="photo">
                                <img
                                    src="https://demo.abi-cms.net/hotel/files/images/home/img_features04.png"
                                    alt="BREAKFAST"
                                />
                                <p className="ic">
                                    <em className="num">04．</em>
                                    <span className="t">BREAKFAST</span>
                                </p>
                            </div>
                            <div className="box_txt">
                                <p className="txt c-jp_b1">
                                    地元のお野菜を使用したこだわりの朝食ブッフェ。
                                    <br />
                                    安心してお食事できるよう感染症対策を徹底した会場設備でお迎えいたします。
                                </p>
                            </div>
                        </li>
                        <li>
                            <div className="photo">
                                <img
                                    src="https://demo.abi-cms.net/hotel/files/images/home/img_features05.png"
                                    alt="WATER"
                                />
                                <p className="ic">
                                    <em className="num">05．</em>
                                    <span className="t">ACCESS</span>
                                </p>
                            </div>
                            <div className="box_txt">
                                <p className="txt c-jp_b1">
                                    名古屋駅から徒歩6分の好立地。ビジネス・観光にとても便利です。
                                </p>
                            </div>
                        </li>
                        <li>
                            <div className="photo">
                                <img
                                    src="https://demo.abi-cms.net/hotel/files/images/home/img_features06.png"
                                    alt="SIMPLICITY and SAFETY"
                                />
                                <p className="ic">
                                    <em className="num">06．</em>
                                    <span className="t">SAFETY</span>
                                </p>
                            </div>
                            <div className="box_txt">
                                <p className="txt c-jp_b1">
                                    24時間スタッフ在中&amp;安心のセキュリティシステムを導入しております。
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>

                {/* おすすめプラン */}
                <div className="con_offers">
                    <h2 className="st c-st1">
                        <i>OFFERS &amp; DEALS</i>
                        <span>おすすめプラン</span>
                    </h2>
                    <div className="box_offers">
                        <ul className="list_offers">
                            <li>
                                <a
                                    href="https://www.489pro-x.com/ja/s/ablivehotel/search/?num=2&amp;plans=5"
                                    target="_blank"
                                    rel="noopener"
                                >
                                    <p className="photo">
                                        <img
                                            src="https://storage.489pro-x.com/ablivehotel/images/3/20221114123736_1.jpg"
                                            alt="SUP（サップ）"
                                        />
                                    </p>
                                    <p className="tit_plan">コースを選べる★ご夕食付きプラン</p>

                                    <p className="txt_price">
                                        お一人様<span>12,000</span>円〜
                                    </p>
                                </a>
                            </li>
                            <li>
                                <a
                                    href="https://www.489pro-x.com/ja/s/ablivehotel/search/?num=2&amp;plans=7"
                                    target="_blank"
                                    rel="noopener"
                                >
                                    <p className="photo">
                                        <img
                                            src="https://storage.489pro-x.com/ablivehotel/images/3/20221223113147_1.jpg"
                                            alt=""
                                        />
                                    </p>
                                    <p className="tit_plan">１棟貸しプラン</p>

                                    <p className="txt_price">
                                        一部屋<span>25,000</span>円〜
                                    </p>
                                </a>
                            </li>
                        </ul>
                        <p className="btn c-btn1 c-btn1-rsv view_pc-tab">
                            <a
                                href="https://www.489pro-x.com/ja/s/ablivehotel/search/?path=recommend"
                                target="_blank"
                                rel="noopener"
                            >
                                VIEW ALL
                            </a>
                        </p>
                    </div>
                </div>

                {/* <!-- /ホテル情報 --> */}
                <section className="con_info">
                    <h2 className="st c-st1">
                        <i>HOTEL INFORMATION</i>
                        <span>ホテル情報</span>
                    </h2>
                    <div className="box_map">
                        <p className="photo">
                            <img
                                src="https://demo.abi-cms.net/hotel/files/images/home/img_access.jpg"
                                alt=""
                            />
                        </p>
                        <div className="wrp_map view_pc-tab">
                            <div className="inner">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d203.8602119087791!2d136.91009254337712!3d35.16252124035721!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x600370cf27dc8f9f%3A0xb5d43ea05cc9b400!2z44Ki44OT44Oq44OWIOWQjeWPpOWxi-acrOekvg!5e0!3m2!1sen!2sjp!4v1687314612842!5m2!1sen!2sjp"
                                    width="100%"
                                    height="100%"
                                    style={{ border: 0 }}
                                    allowFullScreen=""
                                    loading="lazy"
                                ></iframe>{' '}
                            </div>
                        </div>
                    </div>
                    <div className="box_table">
                        <table className="c-table sp_block">
                            <tbody>
                                <tr>
                                    <th>住所</th>
                                    <td>
                                        <em>
                                            〒460-0008　愛知県名古屋市中区栄5-28-12　名古屋若宮ビル
                                        </em>
                                    </td>
                                </tr>
                                <tr>
                                    <th>TEL / FAX</th>
                                    <td>
                                        TEL：<span className="tel">000-000-0000</span>
                                        <i className="line">/</i>
                                        <br className="view_sp" />
                                        FAX：000-000-0000
                                    </td>
                                </tr>
                                <tr>
                                    <th>チェックイン / チェックアウト</th>
                                    <td>15:00&nbsp;/&nbsp;11:00</td>
                                </tr>
                                <tr>
                                    <th>客室</th>
                                    <td>147室</td>
                                </tr>
                                <tr>
                                    <th>支払い方法</th>
                                    <td>
                                        <span>
                                            現金・クレジットカード（VISA、MASTER、JCB、AMEX、DINERS、銀聯カード）
                                        </span>{' '}
                                        <span>
                                            QRコード決済（LINE Pay、PayPay、d払い、au
                                            PAY、Rpay、ALIPAY、WeChatPay）
                                        </span>{' '}
                                    </td>
                                </tr>
                                <tr>
                                    <th>アクセス方法</th>
                                    <td>
                                        <span className="bg">地下鉄矢場町3番出口より徒歩2分</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>駐車場</th>
                                    <td>無し</td>
                                </tr>
                                <tr>
                                    <th>インターネット接続環境</th>
                                    <td>全室Wi-Fi対応</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </>
    )
}
