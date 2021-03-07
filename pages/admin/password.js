import React, { Component } from 'react';
import Head from 'next/head';
import Router from 'next/router';
import {isLogin, isAdmin} from '../../libs/utils';
import Layout, {siteName, siteTitle} from '../../components/layout';
import API from '../../libs/axios';
import {toast} from 'react-toastify';
import {Container, Breadcrumb, Card, Row, Col, Spinner, Button, Form} from 'react-bootstrap';
import { Formik } from 'formik';
import * as yup from 'yup';
import Skeleton from 'react-loading-skeleton';
import {FaSave} from 'react-icons/fa';

const validationSchema = yup.object({
    password: yup.string().required()
    .min(8, "Kata sandi terlalu pendek - minimal 8 karakter.")
    .matches(/(?=.*[0-9])/, "Kata sandi harus kombinasi angka dan huruf.")
    ,
  }); 
class Password extends Component {
    constructor(props) {
        super(props)
        this.state = {
            id: '',
            password: '',
            konfirmasi_password: '',
            loading: true
        }

    }

    componentDidMount = () => {

    const datas = JSON.parse(localStorage.getItem('isAdmin'))
    const id = datas[0].email
    API.GetUserId(id).then(res=>{
        //console.log(res)
        setTimeout(() => this.setState({
            id: id,
            loading: false
          }), 100);
    })
    }            
    
    render() {
        return (
            <Layout admin>
                <Head>
                    <title>Setting - {siteTitle}</title>
                </Head>
           
                <Container fluid>
                <Breadcrumb className="my-3">
                <Breadcrumb.Item>Beranda</Breadcrumb.Item>
                <Breadcrumb.Item active>Ganti Password</Breadcrumb.Item>
                </Breadcrumb>
                    <Row>
                  
                    <Col>
                        <Card body>
                        <h3 className="mb-3">Ganti Password</h3> 
                        {this.state.loading ?
                        <>
                        <Skeleton count={3} height={40} className="mb-1" />
                        <Skeleton width={100} height={40} />
                        </>
                        :
                            <Formik
                            initialValues={{ 
                                id: this.state.id,
                                password: '', 
                                konfirmasi_password: ''
                            }}
                            onSubmit={(values, actions) => {
                                //alert(JSON.stringify(values));
                                if (values.konfirmasi_password === values.password) {
                                API.PutUserPass(values).then(res=>{
                                    //console.log(res)
                                    if (res.status === 1 ) {
                                        toast.success("Data berhasil disimpan", {position: "top-center"});
                                        setTimeout(() => {
                                            Router.reload(); 
                                        }, 1000);
                                    } 
                                    
                                }).catch(err => {
                                    console.log(err.response)
                                    toast.warn("Tidak ada data yang diubah", {position: "top-center"});

                                })
                            } else {
                                toast.warn("Konfirmasi Password tidak sesuai", {position: "top-center"});
                            }
                                
                                setTimeout(() => {
                                actions.setSubmitting(false);
                                }, 1000);
                            }}
                            validationSchema={validationSchema}
                            >
                            {({
                                handleSubmit,
                                handleChange,
                                handleBlur,
                                values,
                                touched,
                                errors,
                                isSubmitting
                            }) => (
                        <Form noValidate onSubmit={handleSubmit}>
                                
                            <Form.Group>
                                <Form.Label>Password Baru</Form.Label>
                                <Form.Control type="password" name="password" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.password} isInvalid={!!errors.password && touched.password} />
                                {errors.password && touched.password && <Form.Control.Feedback type="invalid">{errors.password}</Form.Control.Feedback>}
                            </Form.Group>

                            <Form.Group>
                                <Form.Label>Konfirmasi Password Baru</Form.Label>
                                <Form.Control type="password" name="konfirmasi_password" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.konfirmasi_password} isInvalid={!!errors.konfirmasi_password && touched.konfirmasi_password} />
                                {errors.konfirmasi_password && touched.konfirmasi_password && <Form.Control.Feedback type="invalid">{errors.konfirmasi_password}</Form.Control.Feedback>}
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
                    }
                        </Card>
                    </Col>
                    </Row>
                </Container>
            </Layout>
        )
    }
}

export default Password