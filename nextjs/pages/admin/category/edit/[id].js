import React, { Component } from 'react';
import Head from 'next/head';
import Link from 'next/link';
import Router from 'next/router';
import Layout, { siteTitle } from '../../../../components/layout';
import API from '../../../../lib/axios';
import {Container, Breadcrumb, Card, Spinner, Button, Form} from 'react-bootstrap';
import { FaSave} from 'react-icons/fa';
import {toast} from 'react-toastify';
import Skeleton from 'react-loading-skeleton';
import { Formik } from 'formik';
import * as yup from 'yup';

const validationSchema = yup.object({
  name: yup.string().required()
}); 

class Edit extends Component {
  constructor(props){
    super(props)
    this.state = {
        id: '',
        name: '',
        loading: true
        
    }
} 
static async getInitialProps ({ query }) {
  const id = query.id
  return {
    id: id
  }
}

  componentDidMount = () => {
    const id = this.props.id
    console.log(id)
        API.GetCategoryId(id).then(res=>{
          //console.log(res)
          setTimeout(() => this.setState({
                id: res.data[0].id,
                name : res.data[0].name,
                loading: false
            }), 100);
        })
  }
  render() {
  //const {title,summary,body,image,date,time,created,category_id,user,user_id,url} = this.state;
  return (
    <Layout admin>
            <Head>
                <title>Edit Kategori - {siteTitle}</title>
            </Head>
    <Container fluid>
                    
                { this.state.loading ?
                <>
                         <Skeleton count={4} height={40} className="mb-1" />
                         <Skeleton width={100} height={40} />
                        </>
                        :
                        <>
                      
                        <Breadcrumb className="my-3">
                        <Link href="/admin" passHref><Breadcrumb.Item >Home</Breadcrumb.Item></Link>
                       
                        <Link href="/admin/category" passHref><Breadcrumb.Item >Kategori</Breadcrumb.Item></Link>
                        <Breadcrumb.Item active>Edit</Breadcrumb.Item>
                        </Breadcrumb>
                        
                        <Card className="mb-2" body>
                            <h5 className="mb-3" style={{fontWeight: '400'}}>Edit Kategori</h5>
                            <Formik
                            initialValues={{ 
                                id: this.state.id,
                                name: this.state.name,
                            }}
                            onSubmit={(values, actions) => {
                                //alert(JSON.stringify(values));
                                
                                    API.PutCategory(values).then(res=>{
                                      //console.log(res)
                                      if (res.status == '200' ) {
                                        toast.success("Data berhasil disimpan", {position: "top-center"}); 
                                        setTimeout(() => { 
                                          Router.push('/admin/category');
                                        }, 4000);
                                      } else {
                                          toast.warn("Gagal, periksa kembali", {position: "top-center"}); 
                                      }
                                       
                                    }).catch(err => {
                                        console.log(err.response)
                                        toast.warn("Tidak ada data yang diubah", {position: "top-center"}); 
                                    })
                                
                                setTimeout(() => {
                                actions.setSubmitting(false);
                                }, 1000);
                            }}
                            validationSchema={validationSchema}
                            enableReinitialize={true}
                            >
                            {({
                                handleSubmit,
                                handleChange,
                                handleBlur,
                                setFieldValue,
                                values,
                                touched,
                                errors,
                                isSubmitting
                            }) => (
                        <Form noValidate onSubmit={handleSubmit}>
                            <Form.Group className="mb-3">
                                <Form.Label>Kategori</Form.Label>
                                <Form.Control name="name" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.name} isInvalid={!!errors.name && touched.name} />
                                {errors.name && touched.name && <Form.Control.Feedback type="invalid">{errors.name}</Form.Control.Feedback>}
                            </Form.Group>
                           
                            <Button variant="primary" type="submit" disabled={isSubmitting}>{isSubmitting ? (
                            <>
                            <Spinner
                            as="span"
                            animation="grow"
                            size="sm"
                            role="status"
                            aria-hidden="true"
                            /> Memuat...
                            </>
                            ) : ( <><FaSave/> Simpan</> )}</Button>
       
                     </Form>
                     )}
                    </Formik>

                        </Card>
                       
                        </>
                }
                    
                   
                </Container>
            </Layout>
  );
}
}


export default Edit;