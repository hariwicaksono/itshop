import React, { Component } from "react";
import AOS from 'aos';
import 'bootstrap/scss/bootstrap.scss';
import "../styles/styles.scss"
import 'aos/dist/aos.css';
import 'spin.js/spin.css';
import '@morteza-jamali/lineicons/WebFont/font-css/LineIcons.css';
import 'react-toastify/dist/ReactToastify.css';
import { ToastContainer } from 'react-toastify';
import API from '../lib/axios';
import Navbar from '../components/navbar';
import NavbarA from '../components/navbarA';
import {logout, isLogin, isAdmin} from '../lib/utils';

class MyApp extends Component {
  constructor(props){
    super(props)
    this.state = {
          Pengaturan: [],
          cartCount: 0,
        }
        
    }

  componentDidMount = () => {
    AOS.init();

    const cartData = JSON.parse(localStorage.getItem('cartItem'));
    const cartCount = cartData && cartData.length ? cartData.length : 0;
    this.setState({cartCount: cartCount});

    API.GetSetting().then(res=>{
      this.setState({
          Pengaturan: res.data[0]
      })
    })
    
  }

  render() {
    const { Component, pageProps } = this.props;

    let that = this;
        const cartCount = (cartCount) => {
            that.setState({cartCount: cartCount})
        }
    return (   
    <>


    <Navbar setting={this.state.Pengaturan} cartCount={that.state.cartCount} />
  

    <Component {...pageProps} setting={this.state.Pengaturan} totalCnt={cartCount} />
    <ToastContainer />
    </>
    );
  }
}

export default MyApp;
