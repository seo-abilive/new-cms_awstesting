'use client'
import ReactSlider from 'react-slick'
import 'slick-carousel/slick/slick.css'
import 'slick-carousel/slick/slick-theme.css'

export default function Slider({ settings = {}, className = '', domId = '', children }) {
    return (
        <ReactSlider {...settings} className={className} id={domId}>
            {children}
        </ReactSlider>
    )
}
