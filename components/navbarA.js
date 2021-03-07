import React, {Component} from 'react';
import Head from 'next/head';
import Router from 'next/router';
import Link from 'next/link';
import {Container, Navbar, Nav, NavItem, NavDropdown, Form, FormControl, Button} from 'react-bootstrap';
import API from '../libs/axios';
import {logout, isLogin, isAdmin} from '../libs/utils';
import {ImagesUrl} from '../libs/urls';
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
 if (isAdmin()) {
       const data = JSON.parse(localStorage.getItem('isAdmin'))
       const id = data[0].email
       API.GetUserId(id).then(res=>{
           this.setState({
               id : res.data[0].id,
               name: res.data[0].name,
               email: res.data[0].email,
               loading: false,
               admin: true
           })
       })
           
   }
  else {
    setTimeout(() => this.setState({
          login: true,
          loading: false
      }), 100);
  }
  
  }

  render(){

    return(
     
<Navbar bg="primary" variant="dark" className="shadow border-bottom py-3" expand="lg" sticky="top">
<Container>
{this.state.admin && (
    <Button onClick={this.props.toggleMenu} type="button" className="btn btn-primary text-white btn-sm mr-2">
      <FaBars />
    </Button>
  )}
  <Link href="/" passHref><Navbar.Brand>
    { this.state.loading ?
          <>
            <Skeleton width={180} height={25} />
          </>
        :
        <>
        {this.props.setting.company}
        </>
        }</Navbar.Brand></Link>
  <Navbar.Toggle aria-controls="basic-navbar-nav" />
  <Navbar.Collapse id="basic-navbar-nav">
    <Nav className="mr-auto">
    <Link href="/" passHref><Nav.Link>Home</Nav.Link></Link>
      {/*<Link href="/blog" passHref><Nav.Link>Blog</Nav.Link></Link>
      <NavDropdown title="Dropdown" id="basic-nav-dropdown">
        <Link href="#" passHref><NavDropdown.Item>Action</NavDropdown.Item></Link>
        <Link href="#" passHref><NavDropdown.Item>Another action</NavDropdown.Item></Link>
        <Link href="#" passHref><NavDropdown.Item>Something</NavDropdown.Item></Link>
        <NavDropdown.Divider />
        <Link href="#" passHref><NavDropdown.Item>Separated link</NavDropdown.Item></Link>
      </NavDropdown>*/}
    </Nav>
    <SearchForm/>

    {this.state.login ?
                <>
                <Form inline>
                <Link href="/login" passHref>
                  <Button className="text-light" variant="link"><FaSignInAlt/> Login</Button>
                  </Link>
                </Form>
               
                </>
               :
               <>
               <Nav>
               <NavItem>
               <NavDropdown title=
               {this.state.foto ? (
                <>
                <img
                    alt="Foto"
                    width="30"
                    className="rounded-circle"
                    src={this.state.url+this.state.foto} />
                </>
                    ) : (
                <>
                <img
                    alt="Foto"
                    width="30"
                    className="rounded-circle"
                    src={this.state.url+'no-avatar.png'} />
                </>
                )} id="basic-nav-dropdown" alignRight>
                <NavDropdown.Item>{this.state.email}</NavDropdown.Item>
                <Link href="/admin/password" passHref><NavDropdown.Item><FaKey/> Ganti Password</NavDropdown.Item></Link>
                <NavDropdown.Item onClick={this.Logout} href=''><FaSignOutAlt/> Logout</NavDropdown.Item>
                </NavDropdown>
                </NavItem>
                </Nav>
                </>
                }
    
  </Navbar.Collapse>
  </Container>
</Navbar>
     
    );
  }
}

export default NavbarA;