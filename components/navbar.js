import React, {Component, useState, useContext} from 'react';
import Head from 'next/head';
import Router from 'next/router';
import Link from 'next/link';
import {Container, Navbar, Nav, NavItem, NavDropdown, Form, FormControl, Button, Modal, Spinner} from 'react-bootstrap';
import API from '../libs/axios';
import {logout, isLogin, isAdmin} from '../libs/utils';
import {ImagesUrl} from '../libs/urls';
import SearchForm from './searchForm';
import {FaBars, FaSignInAlt, FaShoppingCart, FaSignOutAlt, FaKey, FaUser} from 'react-icons/fa';
import Skeleton from 'react-loading-skeleton';

function Cart(props) {
  const [show, setShow] = useState(false);

  const handleClose = () => setShow(false);
  const handleShow = () => setShow(true);

  return (
    <>
      <Button variant="light" onClick={handleShow}>
      <FaShoppingCart size="1.5em" style={{color: '#e0a800'}}/> {props.cartCount ? <span className="text-danger">{props.cartCount}</span> : ""} Keranjang
      </Button>

      <Modal show={show} size="lg" onHide={handleClose} animation={false} backdrop="static" keyboard={false}>
        <Modal.Header closeButton>
          <Modal.Title>Keranjang</Modal.Title>
        </Modal.Header>
        <Modal.Body>{props.cartCount ? <span className="text-danger">{props.cartCount}</span> : ""}</Modal.Body>
        <Modal.Footer>
          <Button variant="light" onClick={handleClose}>
            Tutup
          </Button>
          <Button variant="primary" onClick={handleClose}>
            Pesan
          </Button>
        </Modal.Footer>
      </Modal>
    </>
  );
}

class MyNavbar extends Component{
  constructor(props) {
    super(props)
    this.state = {
        loading: true
    }
  }


componentDidMount = () => {
  if (localStorage.getItem('isAdmin')) {
    return( Router.push('/admin') )
  }
  if (isLogin()) {
      const data = JSON.parse(localStorage.getItem('isLogin'))
      const id = data[0].email
      API.GetUserId(id).then(res=>{
          this.setState({
              id : res.data[0].id,
              name: res.data[0].name,
              email: res.data[0].email,
              loading: false,
          })
      })
          
  } 
  else {
    setTimeout(() => this.setState({
          loading: false
      }), 1000);
  }
  
  }

  render(){

    return(
      <>
      <Navbar bg="white" variant="light" className="shadow-sm py-3" expand="lg" sticky="top" >
      <Container>
      
        <Link href="/" passHref>
          <Navbar.Brand >
          {/* this.state.loading ?
                <>
                  <Spinner animation="grow" variant="primary" size="sm" />
                  <Spinner animation="grow" variant="success" size="sm" />
                  <Spinner animation="grow" variant="danger" size="sm" />
                  <Spinner animation="grow" variant="warning" size="sm" />
                </>
              :
              <strong>
             {this.props.setting}
              </strong>
          */}
          <strong>
           {this.props.brandName}
           </strong>
          </Navbar.Brand></Link>
        <Navbar.Toggle aria-controls="basic-navbar-nav" />
        <Navbar.Collapse id="basic-navbar-nav">
          <Nav>
        
          </Nav>
          
         <SearchForm/>
          
          <Nav className="ms-auto">
          
          <Form inline>
          <Link href="/login" passHref>
            <Button variant="light"><FaUser className="text-primary"/> Masuk</Button>
            </Link>
          </Form>
          </Nav>
      
        </Navbar.Collapse>
        </Container>
      </Navbar>

<Navbar bg="primary" variant="dark" className="shadow-sm py-3" expand="lg" sticky="top" >
<Container>

  <Navbar.Toggle aria-controls="basic-navbar-nav" />
  <Navbar.Collapse id="basic-navbar-nav">
    <Nav className="me-auto">
    <NavDropdown title="Produk Kami" id="basic-nav-dropdown">
        <Link href="#" passHref><NavDropdown.Item>Action</NavDropdown.Item></Link>
        <Link href="#" passHref><NavDropdown.Item>Another action</NavDropdown.Item></Link>
        <Link href="#" passHref><NavDropdown.Item>Something</NavDropdown.Item></Link>
        <NavDropdown.Divider />
        <Link href="#" passHref><NavDropdown.Item>Separated link</NavDropdown.Item></Link>
      </NavDropdown>

      <Link href="/blog" passHref><Nav.Link>Produk</Nav.Link></Link>

      <Link href="/blog" passHref><Nav.Link>Blog</Nav.Link></Link>
    
      <Link href="/blog" passHref><Nav.Link>Kontak</Nav.Link></Link>

    </Nav>

    <Nav>
    <Cart cartCount={this.props.cartCount} />
    </Nav>

  </Navbar.Collapse>
  </Container>
</Navbar>
     </>
    );
  }
}

export default MyNavbar;