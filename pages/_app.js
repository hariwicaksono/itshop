import React, { Component } from "react";
import '../styles/bootstrap.css';
import '../styles/globals.css';
import 'spin.js/spin.css';
import 'react-toastify/dist/ReactToastify.css';
import 'slick-carousel/slick/slick.css'; 
import 'slick-carousel/slick/slick-theme.css';
import { ToastContainer } from 'react-toastify';
import API from '../libs/axios';

class MyApp extends Component {
  constructor(props){
    super(props)
    this.state = {
      Pengaturan: []
        }
    }

 

  componentDidMount = () => {
    API.GetSetting().then(res=>{
      this.setState({
          Pengaturan: res.data[0]
      })
    })
  }

  render() {
    const { Component, pageProps } = this.props;

    return (   
    <>
    <Component {...pageProps} setting={this.state.Pengaturan} />
    <ToastContainer />
    </>
    );
  }
}

export default MyApp;
