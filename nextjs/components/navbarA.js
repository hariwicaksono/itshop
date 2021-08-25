import React, {Component} from 'react';
import Head from 'next/head';
import Router from 'next/router';
import Link from 'next/link';
import {Container, Navbar, Nav, NavItem, NavDropdown, Form, FormControl, Button} from 'react-bootstrap';
import API from '../lib/axios';
import {logout, isLogin, isAdmin} from '../lib/utils';
import {ImagesUrl} from '../lib/urls';
import SearchForm from './searchForm';
import {FaBars, FaSignInAlt, FaSignOutAlt, FaKey} from 'react-icons/fa';
import Skeleton from 'react-loading-skeleton';

class NavbarA extends Component{
  constructor(props) {
    super(props)
    this.state = {
        login:false,
        id: '',
        name: '',
        foto:'',
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
  if (!isAdmin()) {
    return( Router.push('/login') )
  }
  API.GetSetting().then(res=>{
    this.setState({
        theme: res.data[0].theme
      })
})

 if (isAdmin()) {
       const data = JSON.parse(localStorage.getItem('isAdmin'))
       const id = data[0].email
       console.log(id)
       API.GetUserId(id).then(res=>{
           this.setState({
            id: res.data[0].id,
               name: res.data[0].name,
               email: res.data[0].email,
               loading: false,
               admin: true
           })
       })
           
  } else {
    setTimeout(() => this.setState({
          login: true,
          loading: false
      }), 100);
  }
  
  }

  render(){

    return(
     
<Navbar bg={this.state.theme} variant="dark" className="shadow border-bottom" expand="md" sticky="top">
<Container fluid>
{this.state.admin && (
    <Button variant={this.state.theme} onClick={this.props.toggleMenu} type="button" className="me-2">
      <FaBars />
    </Button>
  )}
  <Link href="/" passHref><a><Navbar.Brand>{ this.state.loading ?<><Skeleton width={180} height={25} /></>:<>{this.props.setting.company}</>}</Navbar.Brand></a></Link>
  <Navbar.Toggle aria-controls="basic-navbar-nav" className="sub-menu-bar" />
  <Navbar.Collapse id="basic-navbar-nav">
  <ul className="navbar-nav me-auto">
  <li><Link href="/" passHref><a className="nav-link">Home</a></Link></li>
  {/*<NavDropdown title="Dropdown" id="basic-nav-dropdown">
    <Link href="#" passHref><NavDropdown.Item>Action</NavDropdown.Item></Link>
    <Link href="#" passHref><NavDropdown.Item>Another action</NavDropdown.Item></Link>
    <Link href="#" passHref><NavDropdown.Item>Something</NavDropdown.Item></Link>
    <NavDropdown.Divider />
    <Link href="#" passHref><NavDropdown.Item>Separated link</NavDropdown.Item></Link>
  </NavDropdown>*/}
    </ul>

    <SearchForm/>

    {this.state.login ?
    <>
    
    </>
    :
    <>
    <ul id="nav" className="navbar-nav">
    <NavDropdown title=
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
    )} id="basic-nav-dropdown" > 
    <NavDropdown.Item>{this.state.email}</NavDropdown.Item>
    <NavDropdown.Item><Link href="/admin/password" passHref><a><FaKey/> Ganti Password</a></Link></NavDropdown.Item>
    <NavDropdown.Item onClick={this.Logout} href=''><FaSignOutAlt/> Logout</NavDropdown.Item>
    </NavDropdown>
    </ul>
    </>
    }
    
  </Navbar.Collapse>
  </Container>
</Navbar>
     
    );
  }
}

export default NavbarA;