import React, { Component } from 'react';
import Link from 'next/link';
import { Container, Badge, Row, Col, Card } from 'react-bootstrap';
import ReactPaginate from 'react-paginate';
import { ImagesUrl } from '../lib/urls';
import { FiChevronsLeft, FiChevronsRight } from "react-icons/fi";
import parse from 'html-react-parser';

class blogPosts extends Component {
  
    constructor(props){
        super(props)
        this.state={
            url : ImagesUrl(),
            offset: 0,
            perPage: 4,
            currentPage: 0
        }
        this.handlePageClick = this.handlePageClick.bind(this);
    } 

    
    getHandler = () => {
       
                const slice = this.props.data.slice(this.state.offset, this.state.offset + this.state.perPage)
                const ListPost = slice.map((post, key) => (
                    <Col md={6} lg={3} xl={3} key={post.id}>
                        <Card data-aos="fade-down" data-aos-easing="linear" data-aos-delay="50">
                        <Card.Img variant="top" src={this.state.url+post.post_image} alt={post.title} height="140"/>
                        <Card.Body>
                        <p className="text-muted">{post.created_at}<br/>
                        <Link href={"/tag/"+post.category} passHref><a><span className="badge bg-secondary text-light">{post.category}</span></a></Link></p> 
                        <Card.Title className="fw-bold mb-0">
                        <Link href={"/blog/posts/"+post.slug} passHref><a>{post.title}</a></Link>
                        </Card.Title>
                        <>
                        {parse(post.summary, { trim: true })}
                        </>
                        
                        </Card.Body>
                        </Card>
                    </Col>
                ))

                this.setState({
                    pageCount: Math.ceil(this.props.data.length / this.state.perPage),
                   
                    ListPost
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
           <Row className="mb-3" >
            {this.state.ListPost}
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

export default blogPosts;