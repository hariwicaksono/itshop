import React, { Component } from 'react';
import {ImagesUrl} from '../lib/urls';
import {Carousel} from 'react-bootstrap';

const url = ImagesUrl();
class Slideshow extends Component {
    constructor(props){
        super(props)
        this.state={
            
        }
    }
    render() {
        const ListSlideshow = this.props.data.map((s, index) => (
            <Carousel.Item key={index} style={{ position: "relative" }} >
                <img
                className="rounded-4 d-block w-100"
                src={url+s.img_slide}
                height="300"
                alt={s.txt_slide}
                />
            </Carousel.Item>

        ))
        return (
            <Carousel className="my-3" fade data-bs-ride="carousel" prevLabel="" nextLabel="">
                {ListSlideshow}
            </Carousel>
        )
    }
}

export default Slideshow