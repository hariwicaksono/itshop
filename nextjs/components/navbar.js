import React, {Component,useState} from 'react';
import Head from 'next/head';
import Router from 'next/router';
import Link from 'next/link';
import {Container, Navbar, Nav, NavItem, NavDropdown, Form, FormControl, Button, Modal, Badge} from 'react-bootstrap';
import API from '../lib/axios';
import {logout, isLogin, isAdmin} from '../lib/utils';
import {ImagesUrl} from '../lib/urls';
import SearchForm from './searchForm';
import {FaBars, FaSignInAlt, FaSignOutAlt, FaKey,FaUser,FaShoppingCart, FaDatabase} from 'react-icons/fa';
import Skeleton from 'react-loading-skeleton';
import { toast } from 'react-toastify';

function Cart(props) {
  const [show, setShow] = useState(false);

  const handleClose = () => setShow(false);
  const handleShow = () => setShow(true);

  const removeCart = () => {
    localStorage.setItem('cartItem', JSON.stringify([]));
    toast.success("Keranjang dikosongkan", {position: "top-center"});
    setTimeout(()=>{
      Router.reload();
    },4000);
  }

  return (
    <>
      <Button onClick={handleShow} className="btn text-light" >
      <FaShoppingCart size="1.25em" /> {props.cartCount ? <Badge pill bg="danger">{props.cartCount}</Badge> : ""}
      </Button>

      <Modal show={show} size="lg" onHide={handleClose} animation={false} backdrop="static" keyboard={false}>
        <Modal.Header closeButton>
          <Modal.Title>Keranjang Belanja</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          {props.cartCount ? <span className="text-danger">{props.cartCount}</span> : ""}
          <Button variant="danger" onClick={removeCart}>Hapus</Button>
        </Modal.Body>
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
        Menu: [],
        login:false,
        id: '',
        name: '',
        foto:'',
        email:'',
        user: false,
        admin: false,
        url: ImagesUrl(),
        theme: '',
        loading: true
    }
  }
  Logout = () => {
    logout();
}

componentDidMount = () => {

  if(localStorage.getItem('isAdmin')){
    console.log('ADMIN')
    const data = JSON.parse(localStorage.getItem('isAdmin'))
    const id = data[0].email
    console.log(id)
        API.GetUserId(id).then(res=>{
          setTimeout(() => this.setState({
            id: res.data[0].id,
            name: res.data[0].name,
            email: res.data[0].email,
            loading: false,
            login: true
        }), 100);
    })
  } else if(localStorage.getItem('isLogin')){
    console.log('USER')
    this.setState({
      login : true
    })
  } else {
      console.log('ORA LOGIN')
      this.setState({
          login : false
      })
  }
  
  API.GetMenu().then(res => {
    setTimeout(() => this.setState({
          Menu: res.data,
          loading: false,
      }), 100);
  })

  API.GetSetting().then(res=>{
      this.setState({
        theme: res.data[0].theme,
      })
})

//if (isAdmin()) {
  //return( Router.push('/admin') )
//}

  }

  render(){
   const {Menu} = this.state;
    const ListMenu = Menu.map((m, i) => (
        <li key={i}><Link href={m.url} passHref><a className="nav-link"> {m.menu} </a></Link></li>  
    ))
    return(  
      <>   
    <Navbar sticky="top" bg="primary" variant="dark" className="shadow py-3" expand="md" >
    <Container fluid>

      <Link href="/" passHref><Navbar.Brand className="fw-bold">{this.props.setting.company}</Navbar.Brand></Link>
      <Navbar.Toggle aria-controls="basic-navbar-nav" />
      <Navbar.Collapse id="basic-navbar-nav" className="sub-menu-bar">
        <ul className="navbar-nav me-auto">
        { this.state.loading ?<><Skeleton width={180} height={25} /></>:<>{ListMenu}</>}
        </ul>

        <SearchForm/>

        {this.state.login === false ?
    <>
    <Cart cartCount={this.props.cartCount} />
     <Link href="/login" passHref><Button className="btn text-light"><FaUser size="1.25em" /></Button></Link>
    </>
    :
    <>
    <ul id="nav" className="navbar-nav">
    {/*<NavDropdown title=
    {this.state.foto ? (
    <>
    <img
        alt="Foto"
        width="30"
        className="rounded-circle"
        src={this.state.url+this.state.foto} /> {this.state.email}
    </>
        ) : (
    <>
    <img
        alt="Foto"
        width="30"
        className="rounded-circle"
        src={this.state.url+'no-avatar.png'} /> {this.state.email}
    </>
        )} id="basic-nav-dropdown" align="end"> */}
    <NavDropdown title={<><FaUser size="1.25em"/> {this.state.email}</>} id="basic-nav-dropdown" align="end"> 
    <NavDropdown.Item>{this.state.email}</NavDropdown.Item>
    <Link href="/admin" passHref><NavDropdown.Item><FaDatabase/> Dashboard</NavDropdown.Item></Link>
    <Link href="/admin/password" passHref><NavDropdown.Item><FaKey/> Ganti Password</NavDropdown.Item></Link>
    <NavDropdown.Item onClick={this.Logout} href=''><FaSignOutAlt/> Logout</NavDropdown.Item>
    </NavDropdown>
    </ul>
    </>
    }
        
      </Navbar.Collapse>
      </Container>
    </Navbar> 
    </>
    );
  }
}

export default MyNavbar;