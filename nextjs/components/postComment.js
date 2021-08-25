import React, { Component } from 'react'
import Head from 'next/head';
import Router from 'next/router';
import Link from 'next/link';
import API from '../lib/axios';
import {toast} from 'react-toastify';
import {Container, Breadcrumb, Card, Row, Col, Spinner, Button, Form} from 'react-bootstrap';
import { FaSave} from 'react-icons/fa';
import Skeleton from 'react-loading-skeleton';
//import moment from 'moment'
//import 'moment/locale/id'
import { Formik } from 'formik';
import * as yup from 'yup';

const validationSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    body: yup.string().required()
  }); 

class FormComment extends Component {
    constructor(props){
        super(props)
        this.state = {
            post_id: '',
            loading: true
        }
    }

    componentDidMount = () => {
        const id = this.props.postID
        this.setState({
            post_id: id,
            loading: false
        })
    }

    render() {
        return (
                <>
                { this.state.loading ?
                <>
                         <Skeleton count={4} height={40} className="mb-1" />
                         <Skeleton width={100} height={40} />
                        </>
                        :
                        <>
                       
                        <Card className="mb-2" body>
                            <h5 className="mb-3" style={{fontWeight: '400'}}>Tambah Komentar</h5>
                            <Formik
                            initialValues={{ 
                                post_id: this.state.post_id,
                                name: '',
                                email: '',
                                body: ''
                            }}
                            onSubmit={(values, actions) => {
                                //alert(JSON.stringify(values));
                                
                                API.PostComment(values).then(res=>{
                                    //console.log(res)
                                    if (res.status == '201' ) {
                                        toast.success("Komentar berhasil disimpan, menunggu persetujuan admin untuk ditampilkan", {position: "top-center"}); 
                                        setTimeout(() => { 
                                            Router.reload();
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
                                <Form.Label>Nama</Form.Label>
                                <Form.Control name="name" placeholder="Nama Anda" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.name} isInvalid={!!errors.name && touched.name} />
                                {errors.name && touched.name && <Form.Control.Feedback type="invalid">{errors.name}</Form.Control.Feedback>}
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <Form.Label>Email</Form.Label>
                                <Form.Control type="text" name="email" placeholder="Email" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.email} isInvalid={!!errors.email && touched.email} />
                                {errors.email && touched.email && <Form.Control.Feedback type="invalid">{errors.email}</Form.Control.Feedback>}
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <Form.Label>Komentar</Form.Label>
                                <Form.Control as="textarea" rows="6" name="body" placeholder="" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.body} isInvalid={!!errors.body && touched.body} />
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
            </>
        )
    }
}

export default FormComment;