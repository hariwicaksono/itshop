import React, { Component } from 'react';
import Head from 'next/head';
import Link from 'next/link';
import Layout, { siteTitle } from '../../components/layout';
import API from '../../libs/axios';
import { ImagesUrl } from '../../libs/urls';
import { Container, Row, Col, Card } from 'react-bootstrap';
import {} from 'react-icons/md';
import parse from 'html-react-parser';
import Skeleton from 'react-loading-skeleton';
import FormComment from '../../components/postComment';
import Posts from '../../components/blogs';

class Detail extends Component {
  constructor(props){
    super(props)
    this.state = {
        Posts: [],
        loading: true
        
    }
} 
static async getInitialProps ({ query }) {
  const category = query.category
  return {
    category: category
  }
}

  componentDidMount = () => {
    const category = this.props.category
    this.setState({
      tag: category,
    })
        API.GetTag(category).then(res=>{
          //console.log(res)
          setTimeout(() => this.setState({
                Posts: res.data,
                loading: false
            }), 100);
        }).catch(err => {
          console.log(err.response)
      })
        
}
  render() {
  const {tag} = this.state;
  return (
    <Layout>
      <Head>
        <title>Blog {tag} - {siteTitle}</title>
      </Head>

      <Container>
      <main className="py-3">
        { this.state.loading ?
          <>
            <Skeleton count={4} height={40} className="mb-1" />
            <Skeleton width={100} height={40} />
          </>
        :
        <>
         { this.state.loading ?
          <Loader options={options} className="spinner" />
          :
          <>
          <h1 className="h2">Semua Kategori {tag}</h1><hr/>
          <Posts data={this.state.Posts} />
          </>    
        }
        </>
        }           
      </main>
  </Container>
    </Layout>
  );
}
}


export default Detail