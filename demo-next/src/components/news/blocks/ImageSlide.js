'use client'

import Slider from 'react-slick'
import 'slick-carousel/slick/slick.css'
import 'slick-carousel/slick/slick-theme.css'
import '../../../styles/news.css'

export default function ImageSlide({ block = [] }) {
    const settings = {
        dots: true,
        arrows: true,
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        speed: 1000,
        autoplay: true,
        autoplaySpeed: 4000,
        pauseOnHover: true,
        fade: false, // デモサイトのフェード
    }

    // 画像
    const images = block.map((image) => ({ src: image.file_url }))

    return (
        <div className="box_news_parts">
            <Slider {...settings} className="box_slide_parts" id="js-newsSlider">
                {images.map((image, key) => (
                    <div key={key}>
                        <div className="slide">
                            <p className="photo">
                                <img src={image.src} />
                            </p>
                        </div>
                    </div>
                ))}
            </Slider>
            <div className="js-arrows slider_dots" id="js-picArrow"></div>
        </div>
    )
}
