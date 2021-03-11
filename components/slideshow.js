import React, { Component } from 'react';
import {ImagesUrl} from '../libs/urls';
import {Carousel} from 'react-bootstrap';
import Slider from "react-slick";

const url = ImagesUrl();
class Slideshow extends Component {
    constructor(props){
        super(props)
        this.state={
            
        }
    }
    render() {
        const settings = {
            className: "",
            centerMode: false,
            centerPadding: '50px',
            slidesToShow: 1,
            autoplay: true,
            arrows: false,
            dots: true,
            infinite:true,
            swipeToSlide: true,
            responsive: [
                {
                    breakpoint: 992,
                    settings: {
                        centerMode: false,
                        centerPadding: '40px',
                        slidesToShow: 1
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        centerMode: false,
                        centerPadding: '30px',
                        slidesToShow: 1
                    }
                },
                {
                    breakpoint: 576,
                    settings: {
                        centerMode: false,
                        centerPadding: '20px',
                        slidesToShow: 1
                    }
                }
            ]
          };
        const ListSlideshow = this.props.data.map((s, index) => (
            <div key={index} style={{ position: "relative" }} >
                <img
                className="rounded d-block w-100"
                src={url+s.img_slide}
                alt={s.txt_slide}
                style={{objectFit:'cover', width:'100%', height:'350px'}}
                />
            </div>

        ))
        return (
            <Slider {...settings} className="mb-5">
                {ListSlideshow}
            </Slider>
        )
    }
}

export default Slideshow