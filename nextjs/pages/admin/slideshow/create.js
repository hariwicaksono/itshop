import React, { Component } from 'react'
import Head from 'next/head';
import Router from 'next/router';
import Link from 'next/link';
import {isLogin, isAdmin} from '../../../lib/utils';
import Layout, {siteName, siteTitle} from '../../../components/layout';
import API from '../../../lib/axios';
import {toast} from 'react-toastify';
import {Container, Breadcrumb, Card, Row, Col, Spinner, Button, Form} from 'react-bootstrap'
import { FaSave} from 'react-icons/fa';
import Skeleton from 'react-loading-skeleton';
//import moment from 'moment'
//import 'moment/locale/id'
import { Formik } from 'formik'
import * as yup from 'yup'
 
const validationSchema = yup.object({
    text_slide: yup.string().required(),
    foto: yup.mixed().required()
  }); 

class Create extends Component {
    constructor(props){
        super(props)
        this.state = {
            text_slide: '',
            foto: '',
            file: {
                foto: ''
            },
            fotoPreviewUrl: ''
        }
    }
 
    componentDidMount = () => {

    }

    render() {

        return (
            <Layout admin>
            <Head>
                <title>Tambah Slideshow - {siteTitle}</title>
            </Head>
                <Container fluid>
     
                        <Breadcrumb className="my-3">
                        <Link href="/admin" passHref><Breadcrumb.Item >Home</Breadcrumb.Item></Link>
                        <Link href="/admin/slideshow" passHref><Breadcrumb.Item >Slideshow</Breadcrumb.Item></Link>
                        <Breadcrumb.Item active>Tambah</Breadcrumb.Item>
                        </Breadcrumb>
                        
                        <Card className="mb-2" body>
                            <h5 className="mb-3" style={{fontWeight: '400'}}>Tambah Slide</h5>
                            <Formik
                            initialValues={{ 
                                text_slide: '',
                                foto: null,
                            }}
                            onSubmit={(values, actions) => {
                                //alert(JSON.stringify({
                                    //text_slide: values.text_slide,
                                    //foto: values.foto.name}));
                                    
                                API.PostSlideshow(
                                    { 
                                        text_slide: values.text_slide,
                                        foto: values.foto.name
                                    }
                                  ).then(res=>{
                                    //console.log(res)
                                    if (res.status == '201' ) {
                                        toast.success("Data berhasil disimpan", {position: "top-center"}); 
                                        setTimeout(() => { 
                                            Router.push('/admin/slideshow');
                                        }, 4000);
                                    } else {
                                        toast.warn("Gagal, periksa kembali", {position: "top-center"}); 
                                    }
                                }).catch(err => {
                                    console.log(err.response)
                                    toast.warn("Tidak ada data yang diubah", {position: "top-center"}); 
                                })
                               API.PostFoto(values.foto, values.foto.name).then(res => {
                                    //console.log('img_ok')
                                    toast.success("Gambar berhasil disimpan", {position: "top-center"}); 
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
                                <Form.Label>Judul Slide</Form.Label>
                                <Form.Control name="text_slide" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.text_slide} isInvalid={!!errors.text_slide && touched.text_slide} />
                                {errors.text_slide && touched.text_slide && <Form.Control.Feedback type="invalid">{errors.text_slide}</Form.Control.Feedback>}
                            </Form.Group>

                            <Form.Group className="mb-3">
                            <Form.Label htmlFor="foto">Upload Gambar</Form.Label>
                            <Form.File className="form-control" name="foto" id="foto" onChange={(event) => 
                                {
                                setFieldValue("foto", event.currentTarget.files[0]); 
                                this.setState({
                                    fotoPreviewUrl: URL.createObjectURL(event.currentTarget.files[0])
                                })
                                }
                                } onBlur={handleBlur} isInvalid={!!errors.foto && touched.foto} />
                            {errors.foto && touched.foto && <div className="error">{errors.foto}</div>}
                            {this.state.fotoPreviewUrl ? <img src={this.state.fotoPreviewUrl} width="200" alt="" className="mt-2 img-fluid" /> : ""}
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
                   
                </Container>
            </Layout>
        )
    }
}

export default Create;