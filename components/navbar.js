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
      <FaShoppingCart size="1em" className="text-primary"/> {props.cartCount ? <span className="text-danger">{props.cartCount}</span> : ""}
      </Button>

      <Modal show={show} size="lg" onHide={handleClose} animation={false} backdrop="static" keyboard={false}>
        <Modal.Header closeButton>
          <Modal.Title>Keranjang Belanja</Modal.Title>
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
      <Navbar bg="white" variant="light" className="shadow py-3" expand="lg" sticky="top" >
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

          <SearchForm/>
  
        <Nav>

          <Link href="/login" passHref>
            <Button variant="light"><FaUser size="1em" className="text-primary"/></Button>
            </Link>
    
          <Cart cartCount={this.props.cartCount} />

          </Nav>

   
      
        </Container>
      </Navbar>

     </>
    );
  }
}

export default MyNavbar;