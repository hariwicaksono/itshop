import React, { Component } from 'react';
import Link from 'next/link';
import {ImagesUrl} from '../libs/urls';
import {Row, Col, Card} from 'react-bootstrap';

const url = ImagesUrl();

class Catalog extends Component {
    constructor(props){
        super(props)
        this.state={
            
        }
    }
    render() {
        const ListCatalog = this.props.data.map((s, index) => (
            <Col xs="4" sm="3" md="3" lg="2" xl="2" >
               <Link href={"/catalog/"+s.slug} passHref>
               <a>
            <Card body key={index} className="text-center">
            <strong>{s.name}</strong>
            </Card>
            </a>
               </Link>
            </Col>
        ))
        return (
            <Row>
                {ListCatalog}
            </Row>
        )
    }
}

export default Catalog