import React, {Component} from 'react';
import Head from 'next/head';
import Router from 'next/router';
import {isLogin, isAdmin} from '../../libs/utils';
import Layout, {siteName, siteTitle} from '../../components/layout';
import {Container, Row, Col, Card, Jumbotron} from 'react-bootstrap';
import API from '../../libs/axios';
import Skeleton from 'react-loading-skeleton';

class Index extends Component{
  constructor(props) {
    super(props)
    this.state = {
        JumlahBlog: '',
        JumlahKategori: '',
        JumlahKomentar: '',
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
      <Skeleton count={4} height={40} className="mb-1" />
      <Skeleton width={100} height={40} />
      </>
      :
      <>
      <Jumbotron className="mb-3">
        <h2>Selamat Datang di Admin Panel</h2>
      </Jumbotron>

      <Row>
        <Col md={4}>
        <Card bg="info" text="light" body>
              <h5>Jumlah Post</h5>
              <h1>{this.state.JumlahBlog}</h1>
            </Card>
        </Col>
        <Col md={4}>
        <Card bg="success" text="light" body>
              <h5>Jumlah Kategori</h5>
              <h1>{this.state.JumlahKategori}</h1>
            </Card>
        </Col>
        <Col md={4}>
        <Card bg="danger" text="light" body>
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