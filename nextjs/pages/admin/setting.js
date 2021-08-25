import React, { Component } from 'react';
import Head from 'next/head';
import Router from 'next/router';
import {isLogin, isAdmin} from '../../lib/utils';
import {ImagesUrl} from '../../lib/urls';
import Layout, {siteName, siteTitle} from '../../components/layout';
import API from '../../lib/axios';
import {toast} from 'react-toastify';
import {Container, Breadcrumb, Card, Row, Col, Spinner, Button, Form, FormControl, Tabs, Tab} from 'react-bootstrap';
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
const validationSchema2 = yup.object({
    landing_intro: yup.string().required('Field harus diisi'),
    landing_link: yup.string().nullable(),
    foto: yup.mixed().nullable()
  }); 
class Setting extends Component {
    constructor(props) {
        super(props)
        this.state = {
            id: '',
            company: '',
            website: '',
            phone: '',
            email: '',
            landing_intro: '',
            landing_link: '',
            landing_img: '',
            theme: '',
            foto: '',
            file: {
                foto: ''
            },
            fotoPreviewUrl: '',
            url: ImagesUrl(),
            loading: true
        }

    }

    componentDidMount = () => {
    API.GetSetting().then(res=>{
        setTimeout(() => this.setState({
            id: res.data[0].id,
            company: res.data[0].company,
            website: res.data[0].website,
            phone: res.data[0].phone,
            email: res.data[0].email,
            landing_intro: res.data[0].landing_intro,
            landing_link: res.data[0].landing_link,
            landing_img: res.data[0].landing_img,
            theme: res.data[0].theme,
            loading: false
          }), 100);
    })
    }    
    
    reloadData = () => {
        setTimeout(() => { 
         this.componentDidMount()
        }, 1000);
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
                        <>
            <Tabs defaultActiveKey="home" id="uncontrolled-tab-example" variant="pills" className="mb-3 nav-fill" >
                <Tab eventKey="home" title="Home">
                            <Formik
                                initialValues={{ 
                                    id: this.state.id,
                                    company: this.state.company,
                                    website: this.state.website,
                                    phone: this.state.phone,
                                    email: this.state.email,
                                    theme: this.state.theme,
                                }}
                                onSubmit={(values, actions) => {
                                    //alert(JSON.stringify(values));
                                    
                                    API.PutSetting(values).then(res=>{
                                        //console.log(res)
                                        if (res.status == '200' ) {
                                            toast.success("Data berhasil disimpan", {position: "top-center"}); 
                                            setTimeout(() => { 
                                                this.reloadData();
                                            }, 4000);
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
                                    setFieldValue,
                                    touched,
                                    errors,
                                    isSubmitting
                                }) => (
                            <Form noValidate onSubmit={handleSubmit}>
                                    
                                <Form.Group className="mb-3">
                                    <Form.Label>Nama Perusahaan *</Form.Label>
                                    <Form.Control type="text" name="company" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.company} isInvalid={!!errors.company && touched.company} />
                                    {errors.company && touched.company && <Form.Control.Feedback type="invalid">{errors.company}</Form.Control.Feedback>}
                                </Form.Group>

                                <Form.Group className="mb-3">
                                    <Form.Label>Website</Form.Label>
                                    <Form.Control type="text" name="website" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.website} isInvalid={!!errors.website && touched.website} />
                                    {errors.website && touched.website && <Form.Control.Feedback type="invalid">{errors.website}</Form.Control.Feedback>}
                                </Form.Group>

                                <Form.Group className="mb-3">
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

                                <Form.Group>
                                <Form.Label>Tema Warna</Form.Label>
                                    <Form.Row>
    
                                        <div className="form-check form-check-inline">
                                        <input className="form-check-input" type="radio" name="theme" id="inlineRadio1" value="primary" onChange={handleChange} onBlur={handleBlur} checked={values.theme === "primary"} />
                                        <label className="form-check-label" htmlFor="inlineRadio1"><div className="p-3 mb-2 bg-primary text-white"></div></label>
                                        </div>

                                        <div className="form-check form-check-inline">
                                        <input className="form-check-input" type="radio" name="theme" id="inlineRadio2" value="secondary" onChange={handleChange} onBlur={handleBlur} checked={values.theme === "secondary"} />
                                        <label className="form-check-label" htmlFor="inlineRadio2"><div className="p-3 mb-2 bg-secondary text-white"></div></label>
                                        </div>

                                        <div className="form-check form-check-inline">
                                        <input className="form-check-input" type="radio" name="theme" id="inlineRadio3" value="success" onChange={handleChange} onBlur={handleBlur} checked={values.theme === "success"} />
                                        <label className="form-check-label" htmlFor="inlineRadio3"><div className="p-3 mb-2 bg-success text-white"></div></label>
                                        </div>

                                        <div className="form-check form-check-inline">
                                        <input className="form-check-input" type="radio" name="theme" id="inlineRadio4" value="danger" onChange={handleChange} onBlur={handleBlur} checked={values.theme === "danger"} />
                                        <label className="form-check-label" htmlFor="inlineRadio4"><div className="p-3 mb-2 bg-danger text-white"></div></label>
                                        </div>

                                        <div className="form-check form-check-inline">
                                        <input className="form-check-input" type="radio" name="theme" id="inlineRadio5" value="dark" onChange={handleChange} onBlur={handleBlur} checked={values.theme === "dark"} />
                                        <label className="form-check-label" htmlFor="inlineRadio5"><div className="p-3 mb-2 bg-dark text-white"></div></label>
                                        </div>

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
                </Tab>

                <Tab eventKey="landing" title="Landing Page">
                            <Formik
                        initialValues={{ 
                            id: this.state.id,
                            landing_intro: this.state.landing_intro,
                            landing_link: this.state.landing_link,
                            foto: null
                        }}
                        onSubmit={(values, actions) => {
                            //alert(JSON.stringify(values));
                            if (values.foto == null) {
                                API.PutSettingLanding(
                                    { 
                                        id: values.id, 
                                        landing_intro: values.landing_intro,
                                        landing_link: values.landing_link,
                                        foto: this.state.landing_img
                                    }
                                ).then(res=>{
                                    if (res.status == '200' ) {
                                    toast.success("Data berhasil disimpan", {position: "top-center"}); 
                                    setTimeout(() => { 
                                        this.reloadData();
                                      }, 4000);
                                    }  else {
                                    toast.warn("Gagal, periksa kembali", {position: "top-center"}); 
                                }
                                }).catch(err => {
                                    console.log(err.response)
                                    toast.warn("Tidak ada data yang diubah", {position: "top-center"}); 
                                })
                            } else {
                            API.PutSettingLanding(
                                { 
                                    id: values.id, 
                                    landing_intro: values.landing_intro,
                                    landing_link: values.landing_link,
                                    foto: values.foto.name
                                }
                            ).then(res=>{
                                if (res.status == '200' ) {
                                toast.success("Data berhasil disimpan", {position: "top-center"}); 
                                setTimeout(() => { 
                                    this.reloadData();
                                  }, 4000);
                                }  else {
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

                            }
                            
                            setTimeout(() => {
                            actions.setSubmitting(false);
                            }, 1000);
                        }}
                        validationSchema={validationSchema2}
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
                            <Form.Label>Gambar saat ini</Form.Label><br/>
                        <img src={this.state.url+this.state.landing_img} className="img-fluid" width="200"/>
                        </Form.Group>

                        <Form.Group className="mb-3">
                            <Form.Label>Landing Intro *</Form.Label>
                            <Form.Control as="textarea" rows={3} name="landing_intro" onChange={handleChange} onBlur={handleBlur} value={values.landing_intro} isInvalid={!!errors.landing_intro && touched.landing_intro}/>
                            {errors.landing_intro && touched.landing_intro && <Form.Control.Feedback type="invalid">{errors.landing_intro}</Form.Control.Feedback>}
                        </Form.Group>

                        <Form.Group className="mb-3">
                            <Form.Label>Landing Link</Form.Label>
                            <Form.Control type="text" name="landing_link" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.landing_link} isInvalid={!!errors.landing_link && touched.landing_link} />
                            {errors.landing_link && touched.landing_link && <Form.Control.Feedback type="invalid">{errors.landing_link}</Form.Control.Feedback>}
                        </Form.Group>

                        <Form.Group className="mb-3">
                            
                        <Form.Label htmlFor="foto">Landing Gambar *</Form.Label>
                        
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
                        ) : ( <><FaSave/> Upload</> )}</Button>

                    </Form>
                    )}
                    </Formik>
                </Tab>
                        
        </Tabs>


                    
            </>
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