import React, { Component } from 'react';
import Link from 'next/link';
import {ImagesUrl} from '../libs/urls';
import {Row, Col, Card} from 'react-bootstrap';
import Slider from "react-slick";

const url = ImagesUrl();

class Catalog extends Component {
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
            slidesToShow: 5,
            autoplay: false,
            arrows: false,
            dots: false,
            infinite:false,
            swipeToSlide: true,
            responsive: [
                {
                    breakpoint: 992,
                    settings: {
                        centerMode: false,
                        centerPadding: '40px',
                        slidesToShow: 5
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        centerMode: false,
                        centerPadding: '30px',
                        slidesToShow: 5
                    }
                },
                {
                    breakpoint: 576,
                    settings: {
                        centerMode: false,
                        centerPadding: '20px',
                        slidesToShow: 5
                    }
                }
            ]
          };
        const ListCatalog = this.props.data.map((s, index) => (
          
            <Link href={"/catalog/"+s.slug} passHref>
               <a>
            <Card body key={index} className="rounded text-center">
            <strong>{index}</strong>
            </Card>
            </a>
            </Link>
       
        ))
        return (
            <Slider {...settings}>
                {ListCatalog}
            </Slider>
        )
    }
}

export default Catalog