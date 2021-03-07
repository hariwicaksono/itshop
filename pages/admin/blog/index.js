import React, { Component, useState, useMemo } from 'react'
import Head from 'next/head';
import Router from 'next/router';
import Link from 'next/link';
import {isLogin, isAdmin} from '../../../libs/utils';
import {ImagesUrl} from '../../../libs/urls';
import Layout, {siteName, siteTitle} from '../../../components/layout';
import API from '../../../libs/axios';
import {toast} from 'react-toastify';
import {Container, Breadcrumb, Card, Row, Col, Spinner, Button, Form} from 'react-bootstrap';
import { FaTrash, FaPencilAlt, FaUpload} from 'react-icons/fa';
import { Formik } from 'formik';
import * as yup from 'yup';
import Loader from 'react-loader';
import DataTable from 'react-data-table-component';
import styled from 'styled-components';
import Dialog from 'react-bootstrap-dialog';

var options = {lines: 13,length: 20,width: 10,radius: 30,scale: 0.35,corners: 1,color: '#fff',opacity: 0.25,rotate: 0,direction: 1,speed: 1,trail: 60,fps: 20,zIndex: 2e9,top: '50%',left: '50%',shadow: false,hwaccel: false,position: 'absolute'};
const validationSchema = yup.object({
  foto: yup.mixed().required()
}); 

class Blog extends Component {
    constructor(props) {
        super(props)
        this.state = {
            Blog: [],
            Category: [],
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
        API.GetBlog().then(res => {
          if (res.data.length > 0) {
            setTimeout(() => this.setState({
                Blog: res.data,
                loading: false
            }), 100);
          } else {
            setTimeout(() => this.setState({
                error: "No Data Found",
                loading: false
            }), 100);
        }
        }).catch(err => {
          console.log(err.response)
      })
      API.GetCategory().then(res => {
        this.setState({
            Category: res.data,
            loading: false
        })
    })

    }  
    
    render() {
      const columns = [
        {
          name: 'ID',
          selector: 'id',
          sortable: true
        },
        {
          name: 'Gambar',
          sortable: true,
          cell: row => <>
          {row.post_image != null ? 
          <img src={this.state.url+row.post_image} width="60" alt="" onClick={() => {
                this.dialog.show({
                  title: 'Ganti Gambar Post',
                  body: [<Formik key={row.id}
                    initialValues={{ 
                        id: row.id, 
                        foto: row.post_image,
                    }}
                    onSubmit={(values, actions) => {
                        API.PutBlogImage(
                            { 
                                id: values.id, 
                                foto: values.foto.name
                            }
                        ).then(res=>{
                            if (res.status === 1 ) {
                               toast.success("Data berhasil disimpan", {position: "top-center"}); 
                               window.location.href = '/admin/blog';
                            }  
                        })
                        API.PostFoto(values.foto, values.foto.name).then(res => {
                          console.log('img_ok')
                          //toast.success("Gambar berhasil disimpan", {position: "top-center"}); 
                        })
                        
                        setTimeout(() => {
                        actions.setSubmitting(true);
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
                     
                     <Form.Group>
                     <Form.Label>Gambar Post</Form.Label><br/>
                    <img src={this.state.url+row.post_image} className="img-fluid" width="200"/>
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

                    <Button variant="primary" type="submit" disabled={isSubmitting}><FaUpload/> Upload</Button>

             </Form>
             )}
            </Formik>],
                  bsSize: 'lg',
                  actions: [
                    Dialog.CancelAction(() => {
                      console.log('Cancel was clicked!')
                    })
                  ],
                  onHide: (dialog) => {
                    dialog.hide()
                    console.log('closed by clicking background.')
                  }
                })
              }} />
              :
              <><img src={`${process.env.BASE_PATH}/images/no-image.png`} width="60" alt="" onClick={() => {
                this.dialog.show({
                  title: 'Ganti Gambar Post',
                  body: [<Formik key={row.id}
                    initialValues={{ 
                        id: row.id, 
                        foto: row.post_image,
                    }}
                    onSubmit={(values, actions) => {
                        API.PutBlogImage(
                            { 
                                id: values.id, 
                                foto: values.foto.name
                            }
                        ).then(res=>{
                            if (res.status === 1 ) {
                               toast.success("Data berhasil disimpan", {position: "top-center"}); 
                               setTimeout(() => {
                                window.location.href = '/admin/blog';
                               }, 2000);
                            }  
                        })
                        API.PostFoto(values.foto, values.foto.name).then(res => {
                          console.log('img_ok')
                          //toast.success("Gambar berhasil disimpan", {position: "top-center"}); 
                        })
                        
                        setTimeout(() => {
                        actions.setSubmitting(true);
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
                     
                     <Form.Group>
                     <Form.Label>Gambar Post</Form.Label><br/>
                    <img src={`${process.env.BASE_PATH}/images/no-image.png`} className="img-fluid" width="200"/>
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

                    <Button variant="primary" type="submit" disabled={isSubmitting}><FaUpload/> Upload</Button>

             </Form>
             )}
            </Formik>],
                  bsSize: 'lg',
                  actions: [
                    Dialog.CancelAction(() => {
                      console.log('Cancel was clicked!')
                    })
                  ],
                  onHide: (dialog) => {
                    dialog.hide()
                    console.log('closed by clicking background.')
                  }
                })
              }} /></>
            }
          </>,
        },
        {
          name: 'Judul Blog',
          selector: 'title',
          sortable: true,
          cell: row => <>
         {row.title}
          
          </>
        },
        {
          name: 'Kategori',
          sortable: true,
          cell: row => <>
          <Formik
                            initialValues={{ 
                                id: row.id, 
                                category_id: '',
                                
                            }}
                            onSubmit={(values, actions) => {
                                alert('Apakah anda yakin akan mengubah data ini?');
                                API.PutBlogCategory(values).then(res=>{
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
                        <Form onChange={handleSubmit}>
                            <Form.Control as="select" name="category_id" onChange={handleChange} defaultValue={row.category_id} onBlur={handleBlur} size="sm">
                            <option value="">Choose Category</option>
                            {this.state.Category.map((b, i) => (<option value={b.id} key={i}>{isSubmitting ? 
                           "menunggu.." : b.name}</option>))}
 
                            </Form.Control>
                           
                     </Form>
                     )}
                    </Formik>
          </>,
        },
        {
          name: 'Aksi',
          sortable: false,
          cell: row => <><Link href={'/admin/blog/edit/'+row.id} passHref><Button size="sm" title="Edit" alt="Edit"><FaPencilAlt/></Button></Link>&nbsp;
          <Button onClick={() => {
                this.dialog.show({
                  title: 'Konfirmasi',
                  body: 'Apakah anda yakin akan menghapus data ini?',
                  bsSize: 'lg',
                  actions: [
                    Dialog.CancelAction(() => {
                      console.log('Cancel was clicked!')
                    }),
                    Dialog.OKAction(() => {
                      API.DeleteBlog(row.id).then(res => {
                        if (res.status === 1) {
                            toast.success("Hapus data berhasil", {position: "top-center"});
                            setTimeout(() => {
                              Router.push('/admin/blog');
                              }, 2000);
                        } else {
                            console.log('gagal')
                        }
                      })
                    })
                  ],
                  onHide: (dialog) => {
                    dialog.hide()
                    console.log('closed by clicking background.')
                  }
                })
              }} variant="danger" size="sm" title="Hapus" alt="Hapus"><FaTrash/></Button></>,
        },
      ];

      const customStyles = {
        rows: {
          style: {
            fontSize: '1rem',
          }
        },
        headCells: {
          style: {
            fontSize: '1rem',
          },
        },
        cells: {
          style: {
            fontSize: '1rem',
          },
        },
    };
    const TextField = styled.input`
      font-size: 14px;
      height: 34px;
      width: 250px;
      border-radius: 3px;
      border-top-left-radius: 5px;
      border-bottom-left-radius: 5px;
      border-top-right-radius: 0;
      border-bottom-right-radius: 0;
      border: 1px solid #e5e5e5;
      padding: 0 32px 0 16px;
      &:hover {
        cursor: pointer;
      }
    `;
    const ClearButton = styled(Button)`
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
      border-top-right-radius: 5px;
      border-bottom-right-radius: 5px;
      height: 34px;
      width: 32px;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
    `;
    const ExpandedStyle = styled.div`
      padding: 10px;
      display: block;
      width: 100%;

      p {
        font-size: 14px;
        font-weight: 400;
        word-break: break-all;
      }
    `;

    const ExpandedComponent = ({ data }) => (
      <ExpandedStyle>
        <p>
          Tanggal dibuat: {data.created_at ? data.created_at : '-'}<br/>
          Tanggal diubah: {data.updated_at ? data.updated_at : '-'}<br/>
        </p>
      </ExpandedStyle>
    );

    const FilterComponent = ({ filterText, onFilter, onClear }) => (
      <>
      <Link href="/admin/blog/create" passHref><Button variant="primary" style={{ position: 'absolute', left: '0', marginLeft: '15px'}}>Tambah Blog</Button></Link>
        <TextField id="search" type="text" placeholder="Filter By Judul" aria-label="Search Input" value={filterText} onChange={onFilter} />
        <ClearButton variant="secondary" type="button" onClick={onClear}>X</ClearButton>
      </>
    );
    
    const BasicTable = () => {
      const [filterText, setFilterText] = useState('');
      const [resetPaginationToggle, setResetPaginationToggle] = useState(false);
      const filteredItems = this.state.Blog.filter(item => item.title && item.title.toLowerCase().includes(filterText.toLowerCase()) 
       );
    
      const subHeaderComponentMemo = useMemo(() => {
        const handleClear = () => {
          if (filterText) {
            setResetPaginationToggle(!resetPaginationToggle);
            setFilterText('');
          }
        };
    
        return <FilterComponent onFilter={e => setFilterText(e.target.value)} onClear={handleClear} filterText={filterText} />;
      }, [filterText, resetPaginationToggle]);
      
    
      return (
        <DataTable
          title="Semua Blog Post"
          columns={columns}
          data={filteredItems}
          pagination
          paginationResetDefaultPage={resetPaginationToggle} // optionally, a hook to reset pagination to page 1
          subHeader
          subHeaderComponent={subHeaderComponentMemo}
          //selectableRows
          //selectableRowsHighlight
          persistTableHead
          expandableRows
          expandOnRowClicked
          expandableRowsComponent={<ExpandedComponent />}
          customStyles={customStyles}
        />
      );
    };

        return (
          
            <Layout admin>
                <Head>
                    <title>Blog - {siteTitle}</title>
                </Head>
                <Container fluid>
                <Breadcrumb className="my-3">
                <Breadcrumb.Item>Home</Breadcrumb.Item>
                <Breadcrumb.Item active>Blog</Breadcrumb.Item>
                </Breadcrumb>
                    <Row>
                  
                    <Col>

                        <Card body> 
                        { this.state.loading ?
                        <Loader options={options} className="spinner" />
                        
                        :
                        <>
                           
                           <BasicTable />
                           <Dialog ref={(component) => { this.dialog = component }} />
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



export default Blog;