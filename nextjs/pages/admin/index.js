import React, {Component} from 'react';
import Head from 'next/head';
import Router from 'next/router';
import {isLogin, isAdmin} from '../../lib/utils';
import Layout, {siteName, siteTitle} from '../../components/layout';
import {Container, Row, Col, Card} from 'react-bootstrap';
import API from '../../lib/axios';
import Skeleton from 'react-loading-skeleton';

class Index extends Component{
  constructor(props) {
    super(props)
    this.state = {
        JumlahBlog: '',
        JumlahKategori: '',
        JumlahKomentar: '',
        JumlahProduk: '',
        loading: true
    }
}
  componentDidMount = () => {
    API.CountBlog().then(res=>{
      setTimeout(() => this.setState({
          JumlahBlog: res.data,
          loading: false
        }), 100);
    })
    API.CountCategory().then(res=>{
      this.setState({
          JumlahKategori: res.data
      })
    })
    API.CountComment().then(res=>{
      this.setState({
        JumlahKomentar: res.data
      })
    })
    API.CountProduct().then(res=>{
      this.setState({
        JumlahProduk: res.data
      })
    })
  }
  render(){
        
    return(
      <Layout admin>
      <Head>
        <title>Admin - {siteTitle}</title>
      </Head>

      <Container className="my-3">
      
      {this.state.loading ?
    <>
    <Row>
      <Skeleton count={4} height={40} className="mb-1" />
      <Skeleton width={100} height={40} />
      </Row>
      </>
      
      :
      <>
       <Row>
         <Col>
     
        <h1 className="h3 fw-bold">Selamat Datang di Admin Panel</h1>
      
      </Col>
      </Row>
     <Row>
        <Col md={3}>
          <Card bg="light" text="dark" body>
                <h5>Jumlah Produk</h5>
                <h1>{this.state.JumlahProduk}</h1>
              </Card>
        </Col>
        <Col md={3}>
        <Card bg="light" text="dark" body>
              <h5>Jumlah Blog Post</h5>
              <h1>{this.state.JumlahBlog}</h1>
            </Card>
        </Col>
        <Col md={3}>
        <Card bg="light" text="dark" body>
              <h5>Jumlah Kategori</h5>
              <h1>{this.state.JumlahKategori}</h1>
            </Card>
        </Col>
        <Col md={3}>
        <Card bg="light" text="dark" body>
              <h5>Jumlah Komentar</h5>
              <h1>{this.state.JumlahKomentar}</h1>
            </Card>
        </Col>
      </Row>
     
      </>
      }
 </Container>
      </Layout>
    );
  }
}

export default Index;