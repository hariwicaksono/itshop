import React, {Component, useState} from 'react';
import Link from '../libs/link';
import { Collapse} from 'react-bootstrap';
import {FaHome, FaFile, FaComment, FaUser, FaImages, FaFileAlt, FaWrench, FaSlidersH, FaSignOutAlt, FaKey} from 'react-icons/fa';
import { logout, isLogin } from '../libs/utils';

function SubMenu() {
    const [open1, setOpen1] = useState(false);
    const [open2, setOpen2] = useState(false);
    const [open3, setOpen3] = useState(false);
    const [open4, setOpen4] = useState(false);
    return (
        <>
        <li>
            <Link href={'/admin'} activeClassName="active" passHref>
             <a><FaHome size="1.4rem"/> <span>Admin</span></a>
            </Link>
        </li>
        <li>
        <a href='#' onClick={() => setOpen1(!open1)} data-toggle="collapse" aria-controls="collapseBlog" aria-expanded={open1} className="dropdown-toggle">
        <FaFileAlt size="1.4rem"/> <span>Blog</span></a>
      <Collapse in={open1}>
      <ul className="list-unstyled" id="collapseBlog">
      <li>
            <Link href={'/admin/blog'} activeClassName="active" passHref>
             <a><FaFileAlt size="1.4rem"/> <span>Daftar Blog</span></a>
            </Link>
        </li>
        <li>
            <Link href={'/admin/blog/create'} activeClassName="active" passHref>
             <a><FaFileAlt size="1.4rem"/> <span>Tambah</span></a>
            </Link>
        </li>
        <li>
            <Link href={'/admin/blog/category'} activeClassName="active" passHref>
             <a><FaFileAlt size="1.4rem"/> <span>Kategori</span></a>
            </Link>
        </li>
         
      </ul>
      </Collapse>
    </li>
    <li>
          <Link href={'/admin/blog/comment'} activeClassName="active" passHref>
          <a title="Komentar" alt="Komentar"><FaComment size="1.4rem"/> <span>Komentar</span></a>
            </Link>
        </li>
        
        <li>
          <Link href={'/admin/setting'} activeClassName="active" passHref>
          <a title="Pengaturan" alt="Pengaturan"><FaWrench size="1.4rem"/> <span>Pengaturan</span></a>
            </Link>
        </li>
        <li>
          <Link href={'/admin/slideshow'} activeClassName="active" passHref>
          <a title="Slideshow" alt="Slideshow"><FaImages size="1.4rem"/> <span>Slideshow</span></a>
            </Link>
        </li>
        <li>
        <a href='#' onClick={() => setOpen2(!open2)} data-toggle="collapse" aria-controls="collapsePengaturan" aria-expanded={open2} className="dropdown-toggle">
         <FaSlidersH size="1.4rem"/> <span>Profil</span></a>
      <Collapse in={open2}>
      <ul className="list-unstyled" id="collapsePengaturan">
        <li>
          <Link href={'/admin/myprofile'} activeClassName="active" passHref>
          <a title="Profil Saya" alt="Profil Saya"><FaUser size="1.4rem"/> <span>Profil Saya</span></a>
            </Link>
        </li>
          <li>
          <Link href={'/admin/password'} activeClassName="active" passHref>
          <a title="Ganti Password" alt="Ganti Password"><FaKey size="1.4rem"/> <span>Ganti Password</span></a>
            </Link>
          </li>
          <li>
              <Link href='' passHref>
              <a onClick={() => {logout()}} title="Logout" alt="Logout"><FaSignOutAlt size="1.4rem"/> <span>Logout</span></a>
              </Link>
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
            login:false 
        }
    }
    componentDidMount = () => {
        
    }

    render() {
      
    return(
        <>
        <nav id="sidebar" className={this.props.showMenu ? 'shadow' : 'shadow active' }>
        <ul className="list-unstyled components">
                    
        <SubMenu/>
 
        </ul>
        </nav>
      
        </>

    )

}
}

export default Sidebar