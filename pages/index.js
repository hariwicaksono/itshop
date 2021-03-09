import React, {Component} from 'react';
import Head from 'next/head';
import Layout, {siteName, siteTitle} from '../components/layout';
import API from '../libs/axios';
import {Container, Alert, Row, Col, Spinner, Button, Form} from 'react-bootstrap';
import Slideshow from '../components/slideshow';
import Loader from 'react-loader';
import Skeleton from 'react-loading-skeleton';
import Blog from '../components/blogs';
import Catalog from '../components/catalog';
import {FaExclamationTriangle} from 'react-icons/fa';

var options = {lines: 13,length: 20,width: 10,radius: 30,scale: 0.35,corners: 1,color: '#fff',opacity: 0.25,rotate: 0,direction: 1,speed: 1,trail: 60,fps: 20,zIndex: 2e9,top: '50%',left: '50%',shadow: false,hwaccel: false,position: 'absolute'};

class Index extends Component{
  constructor(props) {
    super(props)
    this.state = {
        Products: [],
        Slideshow: [],
        Catalog: [],
        Posts: [],
        loading: true
    }
  
}
    componentDidMount() {
      if (!localStorage.getItem('cartItem')) {
        const array = '[]';
        localStorage.setItem('cartItem',array);
      }
      API.GetSlideshow().then(res => {
        setTimeout(() => this.setState({
              Slideshow: res.data,
              loading: false
          }), 200);
      })
      API.GetBlog().then(res => {
        this.setState({
            Posts: res.data,
        });
      })
      API.GetCatalog().then(res => {
        this.setState({
            Catalog: res.data,
        });
      })
  } 
  render(){
        
    return(
      <Layout home>
      <Head>
        <title>{siteTitle}</title>
      </Head>

      <main className="py-3">
        <Container>
        {/*<Alert variant="success">
          <small><h1 className="h5"><FaExclamationTriangle/> Informasi</h1>Selamat Datang di <strong>React Next.js App</strong> {this.props.setting.company}. Informasi lebih lanjut hubungi Telp/WA di {this.props.setting.phone} atau {this.props.setting.email}</small>
    </Alert>*/}

        { this.state.loading ?
          <>
            <Skeleton height={400} />
          </>
        :
        <>
        <Slideshow data={this.state.Slideshow} /> 
        </>
        }

      <Catalog data={this.state.Catalog} /> 
       
        <Row>
          <Col md="12">
         
          { this.state.loading ?
          <>

          </>
          :
          <>
          <section className="">
          <h2 className="text-center">Produk Terlaris</h2>

          </section>

          <section className="">
          <h2 className="text-center">Blog</h2>
            <Blog data={this.state.Posts} />
          </section>

          <section className="">
          <h2 className="text-center">Ulasan</h2>

          </section>
          </>
          }
          </Col>
        </Row>
        
        </Container>
      </main>
      </Layout>
    );
  }
}

export default Index;