import React, {Component, useState} from 'react';
import Link from '../lib/link';
import Router from 'next/router';
import { Collapse, Button} from 'react-bootstrap';
import {FaHome, FaFile, FaComment, FaUser, FaImages, FaArrowCircleLeft,FaArrowCircleRight, FaFileAlt, FaWrench, FaSlidersH, FaSignOutAlt, FaKey} from 'react-icons/fa';
import { logout, isLogin, isAdmin } from '../lib/utils';

function SubMenu() {
    const [open1, setOpen1] = useState(false);
    const [open2, setOpen2] = useState(false);
    const [open3, setOpen3] = useState(false);
    const [open4, setOpen4] = useState(false);
    return (
        <>
        <li>
            <Link href={'/admin'} activeClassName="active" passHref><><FaHome size="1.4rem"/> <span>Admin</span></></Link>
        </li>
        <li>
        <a href='#' onClick={() => setOpen1(!open1)} data-toggle="collapse" aria-controls="collapseBlog" aria-expanded={open1} className="dropdown-toggle">
        <FaFileAlt size="1.4rem"/> <span>Blog</span></a>
      <Collapse in={open1}>
      <ul className="list-unstyled" id="collapseBlog">
      <li>
          <Link href={'/admin/blog'} activeClassName="active" passHref><><FaFileAlt size="1.4rem"/> <span>Daftar Blog</span></></Link>
        </li>
        <li>
          <Link href={'/admin/blog/create'} activeClassName="active" passHref><><FaFileAlt size="1.4rem"/> <span>Tambah</span></></Link>
        </li>
         
      </ul>
      </Collapse>
    </li>

    <li>
      <Link href={'/admin/category'} activeClassName="active" passHref><><FaFileAlt size="1.4rem"/> <span>Kategori</span></></Link>
    </li>

    <li>
      <Link href={'/admin/blog/comment'} activeClassName="active" passHref><><FaComment size="1.4rem"/> <span>Komentar</span></></Link>
    </li>
        
        <li>
          <Link href={'/admin/setting'} activeClassName="active" passHref><><FaWrench size="1.4rem"/> <span>Pengaturan</span></></Link>
        </li>
        <li>
          <Link href={'/admin/slideshow'} activeClassName="active" passHref><><FaImages size="1.4rem"/> <span>Slideshow</span></></Link>
        </li>
        <li>
        <a href='#' onClick={() => setOpen2(!open2)} data-toggle="collapse" aria-controls="collapsePengaturan" aria-expanded={open2} className="dropdown-toggle">
         <FaSlidersH size="1.4rem"/> <span>Profil</span></a>
      <Collapse in={open2}>
      <ul className="list-unstyled" id="collapsePengaturan">
        <li>
          <Link href={'/admin/myprofile'} activeClassName="active" passHref><><FaUser size="1.4rem"/> <span>Profil Saya</span></></Link>
        </li>
          <li>
          <Link href={'/admin/password'} activeClassName="active" passHref><><FaKey size="1.4rem"/> <span>Ganti Password</span></></Link>
          </li>
          <li>
            <Link href='' onClick={() => {logout()}} passHref><><FaSignOutAlt size="1.4rem"/> <span>Logout</span></></Link>
          </li>
      </ul>
      </Collapse>
    </li>
      
    </>
    );
  }
class Sidebar extends Component {
    constructor(props) {
        super(props)
        this.state = {
            login:false ,
            showMenu: true,
        }
        this.toggleMenu = this.toggleMenu.bind(this)
    }
    toggleMenu = function() {
      this.setState({ showMenu: !this.state.showMenu });
    }
    componentDidMount = () => {
      if (!isAdmin()) {
        return( Router.push('/login') )
      }
    }

    render() {
      
    return(
        <>
        
        <nav id="sidebar" className={this.state.showMenu ? 'shadow active' : 'shadow ' }>
        <ul className="list-unstyled components">
        <SubMenu/>
        <li>
          <a onClick={this.toggleMenu} className="text-primary text-center">
          {this.state.showMenu ?  <FaArrowCircleRight size="1.4rem"/> : <FaArrowCircleLeft size="1.4rem"/> }
         
        </a>
          </li>
        </ul>
        </nav>
        </>

    )

}
}

export default Sidebar