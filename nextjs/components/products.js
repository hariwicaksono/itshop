import React, { Component } from 'react';
import Link from 'next/link';
import { Container, Row, Col, Card } from 'react-bootstrap';
import ReactPaginate from 'react-paginate';
import {ImagesUrl} from '../lib/urls';
import { FiChevronsLeft, FiChevronsRight, FiShoppingCart } from "react-icons/fi";
import _ from 'underscore';
import { toast } from 'react-toastify';


class Products extends Component {
  
    constructor(props){
        super(props)
        this.state={
            url : ImagesUrl(),
            offset: 0,
            perPage: 8,
            currentPage: 0
        }
        this.handlePageClick = this.handlePageClick.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    } 

    

    handleSubmit(produk, key) {
        const array = [];
        array.push(produk);
        const cartData = JSON.parse(localStorage.getItem('cartItem'));
        const findManData = _.findWhere(cartData, {id: produk.id});
        if (findManData) {
            for (let i = 0; i < cartData.length; i++) {
                if (produk.id === cartData[i].id) {
                    cartData[i].count += 1; 
                    break;
                }
            }
            localStorage.setItem('cartItem',JSON.stringify(cartData));
        } else {
            if (cartData) {
                cartData.push({...produk,count:1})
                localStorage.setItem('cartItem',JSON.stringify(cartData));
            } else {
                localStorage.setItem('cartItem',array);
            }
        }
        toast.success("Produk berhasil masuk keranjang", {position: "top-center"});
        setTimeout(()=>{
          this.props.totalCnt(JSON.parse(localStorage.getItem('cartItem')) ? JSON.parse(localStorage.getItem('cartItem')).length : 0);
        },1000);
    }
   
    getHandler = () => {
       
                const slice = this.props.data.slice(this.state.offset, this.state.offset + this.state.perPage)
                const ListProduk = slice.map((produk, key) => (
                    <Col md={3} key={produk.id}>
                        
                    <Card className="shadow-sm mb-3">
                        {produk.post_image !== null ?
                        <Card.Img variant="top" src={this.state.url+produk.post_image} alt={produk.title} height="220"/>
                        :
                        <Card.Img variant="top" src={this.state.url+"img_product.jpg"} alt={produk.title} height="220"/>
                        }
                    
                        <Card.Body className="pt-1">
                        <Card.Text className="mb-0" style={{fontSize: '1.125rem'}}><Link href={"/products/"+produk.slug} passHref><a>{produk.title}</a></Link></Card.Text>
                            <Card.Text className="text-danger" style={{fontSize: '1rem'}}>Rp.{produk.price}</Card.Text>
                            <button type="submit" name="submit" defaultValue="Keranjang" className="btn btn-secondary btn-block" onClick={() => this.handleSubmit(produk, key)}>Beli <FiShoppingCart /></button>
                        </Card.Body>
                    </Card>
                    
                    </Col>
                ))

                this.setState({
                    pageCount: Math.ceil(this.props.data.length / this.state.perPage),
                   
                    ListProduk
                })

    
    }
    handlePageClick = (e) => {
        const selectedPage = e.selected;
        const offset = selectedPage * this.state.perPage;

        this.setState({
            currentPage: selectedPage,
            offset: offset
        }, () => {
            this.getHandler()
        });

    };
    componentDidMount = () => {
        this.getHandler()
  }
    render() {
        return (
            <>
            <Row>
                {this.state.ListProduk}
            </Row>
            <div className="py-3">
                <ReactPaginate
                containerClassName="pagination"
                breakClassName="page-item"
                breakLinkClassName="page-link"
                pageClassName="page-item"
                previousClassName="page-item"
                nextClassName="page-item"
                pageLinkClassName="page-link"
                previousLinkClassName="page-link"
                nextLinkClassName="page-link"
                activeClassName="active"
                previousLabel={<FiChevronsLeft/>}
                nextLabel={<FiChevronsRight/>}
                pageCount={this.state.pageCount}
                marginPagesDisplayed={2}
                pageRangeDisplayed={3}
                onPageChange={this.handlePageClick}
                />
            </div>
            </>
        )
    }
}

export default Products