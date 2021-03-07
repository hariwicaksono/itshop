import React, { Component } from 'react'
import Head from 'next/head';
import Router from 'next/router';
import Link from 'next/link';
import {isLogin, isAdmin} from '../../../libs/utils';
import Layout, {siteName, siteTitle} from '../../../components/layout';
import API from '../../../libs/axios';
import {toast} from 'react-toastify';
import {Container, Breadcrumb, Card, Row, Col, Spinner, Button, Form} from 'react-bootstrap';
import { FaSave} from 'react-icons/fa';
import Skeleton from 'react-loading-skeleton';
//import moment from 'moment'
//import 'moment/locale/id'
import { Formik } from 'formik';
import * as yup from 'yup';
import { Editor } from "@tinymce/tinymce-react";

const validationSchema = yup.object({
    title: yup.string().required(),
    category_id: yup.string().required(),
    date: yup.string().required(),
    time: yup.string().required(),
    summary: yup.string().required(),
    body: yup.string().required(),
    foto: yup.mixed().required()
  }); 

class Create extends Component {
    constructor(props){
        super(props)
        this.state = {
            category_id: '',
            user_id: '',
            category: [],
            date: '',
            time: '',
            foto: '',
            file: {
                foto: ''
            },
            fotoPreviewUrl: '',
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

        API.GetCategory().then(res => {
            this.setState({
                category: res.data,
                loading: false
            })
        })
    }

    render() {
        const ListCategory= this.state.category.map((b, i) => (
            <option value={b.id} key={i}>{b.name}</option>      
        ))
        return (
            <Layout admin>
            <Head>
                <title>Tambah Blog - {siteTitle}</title>
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
                        <Breadcrumb.Item active>Tambah</Breadcrumb.Item>
                        </Breadcrumb>
                        
                        <Card className="mb-2" body>
                            <h5 className="mb-3" style={{fontWeight: '400'}}>Tambah Blog</h5>
                            <Formik
                            initialValues={{ 
                                category_id: '',
                                user_id: this.state.id,
                                title: '',
                                summary: '',
                                body: '',
                                date: '',
                                time: '',
                                foto: null,
                            }}
                            onSubmit={(values, actions) => {
                                //alert(JSON.stringify({
                                    //category_id: values.category_id,
                                    //user_id: this.state.id,
                                    //title: values.title,
                                    //summary: values.summary,
                                    //body: values.body,
                                    //date: values.date,
                                    //time: values.time,
                                    //foto: values.foto.name}));
                                
                                API.PostBlog(
                                    { 
                                        category_id: values.category_id,
                                        user_id: this.state.id,
                                        title: values.title,
                                        summary: values.summary,
                                        body: values.body,
                                        date: values.date,
                                        time: values.time,
                                        foto: values.foto.name
                                    }
                                  ).then(res=>{
                                    //console.log(res)
                                    if (res.status === 1 ) {
                                        toast.success("Data berhasil disimpan", {position: "top-center"}); 
                                        setTimeout(() => { 
                                            Router.push('/admin/blog');
                                        }, 2000);
                                    } else {
                                        toast.warn("Gagal, periksa kembali", {position: "top-center"}); 
                                    }
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
                            //enableReinitialize={true}
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
                                <Form.Label>Judul Blog</Form.Label>
                                <Form.Control name="title" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.title} isInvalid={!!errors.title && touched.title} />
                                {errors.title && touched.title && <Form.Control.Feedback type="invalid">{errors.title}</Form.Control.Feedback>}
                            </Form.Group>

                            <Form.Group>
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

                            <Form.Group>
                            <Form.Label>Kategori</Form.Label>
                            <Form.Control as="select" name="category_id" onChange={handleChange} onBlur={handleBlur} value={values.category_id} isInvalid={!!errors.category_id && touched.category_id}>
                            <option value="">Pilih Kategori</option>
                            {ListCategory}
                            </Form.Control>
                            {errors.category_id && touched.category_id && <Form.Control.Feedback type="invalid">{errors.category_id}</Form.Control.Feedback>}
                            </Form.Group>

                            <Form.Group>
                            <Row>
                            <Col>
                                <Form.Label>Tanggal</Form.Label>
                                <Form.Control type="date" name="date" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.date} isInvalid={!!errors.date && touched.date} />
                                {errors.date && touched.date && <Form.Control.Feedback type="invalid">{errors.date}</Form.Control.Feedback>}
                            </Col>
                            <Col>
                            <Form.Label>Jam</Form.Label>
                                <Form.Control type="time" name="time" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.time} isInvalid={!!errors.time && touched.time} />
                                {errors.time && touched.time && <Form.Control.Feedback type="invalid">{errors.time}</Form.Control.Feedback>}
                            </Col>
                            </Row>
                            </Form.Group>

                            <Form.Group>
                                <Form.Label>Ringkasan</Form.Label>
                                <Editor 
                                apiKey="vffx7rg47lbz69xfs80qajyt04jjsxtihahl5gp1rsek0vnt" 
                                init={{
                                  height: 150,
                                  menubar: false
                                }}
                                onEditorChange={handleChange} rows="2" name="summary" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.summary} isInvalid={!!errors.summary && touched.summary} />
                                {errors.summary && touched.summary && <Form.Control.Feedback type="invalid">{errors.summary}</Form.Control.Feedback>}
                            </Form.Group>


                            <Form.Group>
                                <Form.Label>Isi Blog</Form.Label>
                                <Editor 
                                apiKey="vffx7rg47lbz69xfs80qajyt04jjsxtihahl5gp1rsek0vnt" 
                                init={{
                                  height: 250,
                                  menubar: false
                                }}
                                onEditorChange={handleChange} rows="4" name="body" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.body} isInvalid={!!errors.body && touched.body} />
                                {errors.body && touched.body && <Form.Control.Feedback type="invalid">{errors.body}</Form.Control.Feedback>}
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