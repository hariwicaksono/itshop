import React, { Component } from 'react';
import Head from 'next/head';
import Router from 'next/router';
import {isLogin, isAdmin} from '../../libs/utils';
import Layout, {siteName, siteTitle} from '../../components/layout';
import API from '../../libs/axios';
import {toast} from 'react-toastify';
import {Container, Breadcrumb, Card, Row, Col, Spinner, Button, Form} from 'react-bootstrap';
import {Formik} from 'formik';
import * as yup from 'yup';
import Skeleton from 'react-loading-skeleton';
import {FaSave} from 'react-icons/fa';

const validationSchema = yup.object({
    company: yup.string().required('Field harus diisi'),
    website: yup.string().required('Field harus diisi'),
    phone: yup.string().required('Field harus diisi'),
    email: yup.string().required('Field harus diisi')
  }); 
class Setting extends Component {
    constructor(props) {
        super(props)
        this.state = {
            company: '',
            website: '',
            phone: '',
            email: '',
            loading: true
        }

    }

    componentDidMount = () => {

    API.GetSetting().then(res=>{
        setTimeout(() => this.setState({
            company: res.data[0].company,
            website: res.data[0].website,
            phone: res.data[0].phone,
            email: res.data[0].email,
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
                <Container className="my-3" fluid>
                <Breadcrumb className="mb-2">
                <Breadcrumb.Item>Admin</Breadcrumb.Item>
                <Breadcrumb.Item active>Pengaturan</Breadcrumb.Item>
                </Breadcrumb>
                    <Row>
                  
                    <Col>

                        <Card body>
                        <h3 className="mb-3">Pengaturan</h3> 
                        {this.state.loading ?
                        <>
                        <Skeleton count={4} height={40} className="mb-1" />
                        <Skeleton width={100} height={40} />
                        </>
                        :
                        <Formik
                            initialValues={{ 
                                company: this.state.company,
                                website: this.state.website,
                                phone: this.state.phone,
                                email: this.state.email
                            }}
                            onSubmit={(values, actions) => {
                                //alert(JSON.stringify(values));
                                
                                API.PutSetting(values).then(res=>{
                                    //console.log(res)
                                    if (res.status === 1 ) {
                                        toast.success("Data berhasil disimpan", {position: "top-center"}); 
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
                                values,
                                touched,
                                errors,
                                isSubmitting
                            }) => (
                        <Form noValidate onSubmit={handleSubmit}>
                                
                            <Form.Group>
                                <Form.Label>Nama Perusahaan *</Form.Label>
                                <Form.Control type="text" name="company" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.company} isInvalid={!!errors.company && touched.company} />
                                {errors.company && touched.company && <Form.Control.Feedback type="invalid">{errors.company}</Form.Control.Feedback>}
                            </Form.Group>

                            <Form.Group>
                                <Form.Label>Website</Form.Label>
                                <Form.Control type="text" name="website" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.website} isInvalid={!!errors.website && touched.website} />
                                {errors.website && touched.website && <Form.Control.Feedback type="invalid">{errors.website}</Form.Control.Feedback>}
                            </Form.Group>

                            <Form.Group >
                            <Form.Row>
                                <Col>
                                <Form.Label>Telepon *</Form.Label>
                                <Form.Control type="text" name="phone" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.phone} isInvalid={!!errors.phone && touched.phone} />
                                {errors.phone && touched.phone && <Form.Control.Feedback type="invalid">{errors.phone}</Form.Control.Feedback>}
                                </Col>
                                <Col>
                                <Form.Label>Email *</Form.Label>
                                <Form.Control type="text" name="email" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.email} isInvalid={!!errors.email && touched.email} />
                                {errors.email && touched.email && <Form.Control.Feedback type="invalid">{errors.email}</Form.Control.Feedback>}
                                </Col>
                               
                            </Form.Row>
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
 
export default Setting;