import React, { Component } from 'react'
import Head from 'next/head';
import Router from 'next/router';
import Link from 'next/link';
import {isLogin, isAdmin} from '../../../../libs/utils';
import Layout, {siteName, siteTitle} from '../../../../components/layout';
import API from '../../../../libs/axios';
import {toast} from 'react-toastify';
import {Container, Breadcrumb, Card, Row, Col, Spinner, Button, Form} from 'react-bootstrap'
import { FaSave} from 'react-icons/fa';
import Skeleton from 'react-loading-skeleton';
//import moment from 'moment'
//import 'moment/locale/id'
import { Formik } from 'formik'
import * as yup from 'yup'

const validationSchema = yup.object({
    name: yup.string().required(),
  }); 

class Create extends Component {
    constructor(props){
        super(props)
        this.state = {
            user_id: '',
            name: '',
            loading: true
        }
    }
 
    componentDidMount = () => {
        const datas = JSON.parse(localStorage.getItem('isAdmin'))
        const id = datas[0].id
        this.setState({
            id: id,
            loading: false
        })
    }

    render() {

        return (
            <Layout admin>
            <Head>
                <title>Tambah Kategori - {siteTitle}</title>
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
                        <Link href="/admin/blog" passHref><Breadcrumb.Item >Blog</Breadcrumb.Item></Link>
                        <Link href="/admin/blog/category" passHref><Breadcrumb.Item >Kategori</Breadcrumb.Item></Link>
                        <Breadcrumb.Item active>Tambah</Breadcrumb.Item>
                        </Breadcrumb>
                        
                        <Card className="mb-2" body>
                            <h5 className="mb-3" style={{fontWeight: '400'}}>Tambah Kategori</h5>
                            <Formik
                            initialValues={{ 
                                user_id: this.state.id,
                                name: ''
                            }}
                            onSubmit={(values, actions) => {
                                //alert(JSON.stringify(values));
                                
                                API.PostCategory(values).then(res=>{
                                    //console.log(res)
                                    if (res.status === 1 ) {
                                        toast.success("Data berhasil disimpan", {position: "top-center"}); 
                                        setTimeout(() => { 
                                            Router.push('/admin/blog/category');
                                        }, 2000);
                                    } else {
                                        toast.warn("Gagal, periksa kembali", {position: "top-center"}); 
                                    }
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
                             
                            <Form.Group>
                                <Form.Label>Nama</Form.Label>
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
        )
    }
}

export default Create;