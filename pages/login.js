import React, { Component } from 'react';
import Head from 'next/head';
import Router from 'next/router';
import Link from 'next/link';
import Layout, {siteName, siteTitle} from '../components/layout';
import {Container, Card, Row, Col, Spinner, Button, Form} from 'react-bootstrap';
import API from '../libs/axios';
import {ImagesUrl} from '../libs/urls';
import { toast } from 'react-toastify';
import { Formik } from 'formik';
import * as yup from 'yup';

const validationSchema = yup.object({
  username: yup.string().required('Username harus diisi'),
  password: yup.string().required('Password harus diisi')
}); 

class Login extends Component {
  componentDidMount = () => {
    if (localStorage.getItem('isLogin')) {
      return( Router.push('/dashboard') )
    }
}
  render() {
  return (
    <Layout login>
      <Head>
        <title>Login - {siteTitle}</title>
      </Head>

      <main className="auth">
      <header className="auth-header">
        <h2>
        <span>{siteName}</span>
        </h2>
        {/*<p>Belum punya akun? <a href="auth-signup.html">Daftar</a>
        </p>*/}
      </header>
      
        <Formik
        initialValues={{ username: '', password: '' }}
        onSubmit={(values, actions) => {
            //alert(JSON.stringify(values));
            API.PostLogin(values).then(res=>{
                if (res.id === "1" ) {
                    localStorage.setItem('isLogin',JSON.stringify(res.data))
                    toast.success("Login Berhasil", {position: "top-center"}); 
                    setTimeout(()=>{
                        Router.push('/')
                    },2000)
                } else if (res.id === "2" ) {
                    localStorage.setItem('isAdmin',JSON.stringify(res.data))
                    toast.success("Login Berhasil", {position: "top-center"});
                    setTimeout(()=>{
                        Router.push('/admin')
                    },2000);
                } else {
                  toast.warn("Periksa kembali username dan password anda", {position: "top-center"}); 
                }
            })
            setTimeout(() => {
            actions.setSubmitting(false);
            }, 1000);
        }}
        validationSchema={validationSchema}
        >
        {({handleSubmit,handleChange,handleBlur,values,touched,errors,isSubmitting}) => (
    <Form noValidate onSubmit={handleSubmit} className="auth-form pb-4">
        <Form.Group>
            <Form.Label className="text-left">Email</Form.Label>
            <Form.Control type="text" name="username" placeholder="your@email.com" className="form-control" onChange={handleChange} onBlur={handleBlur} value={values.username} isInvalid={!!errors.username && touched.username} />
            {errors.username && touched.username && <Form.Control.Feedback type="invalid">{errors.username}</Form.Control.Feedback>}
        </Form.Group>
        <Form.Group>
            <Form.Label className="text-left">Password</Form.Label>
            <Form.Control type="password" name="password" placeholder="Password" className="form-control" onChange={handleChange} onBlur={handleBlur} isInvalid={!!errors.password && touched.password} />
            {errors.password && touched.password && <Form.Control.Feedback type="invalid">{errors.password}</Form.Control.Feedback>}
        </Form.Group>

        <Button block variant="primary" type="submit" disabled={isSubmitting}>
        {isSubmitting ? (
        <>
        <Spinner as="span" animation="grow" size="sm" role="status" aria-hidden="true" /> Memuat...
        </>
        ) : (
        <>Masuk</>
        )}
        </Button>
        {/*<div className="text-center pt-3 pb-4">
          <a href="auth-recovery-username.html" className="link">Daftar akun</a> <span className="mx-2">·</span> <a href="auth-recovery-password.html" className="link">Lupa Password?</a>
        </div>*/}
    </Form>

    )}
    </Formik>
    
    <div className="py-3">
    <Link href="/">
            <a>← Kembali ke home</a>
          </Link>
    </div>
    </main>

    </Layout>
  );
  }
}

export default Login;
