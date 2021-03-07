import React, { Component, useState, useMemo } from 'react'
import Head from 'next/head';
import Router from 'next/router';
import Link from 'next/link';
import {isLogin, isAdmin} from '../../../../libs/utils';
import {ImagesUrl} from '../../../../libs/urls';
import Layout, {siteName, siteTitle} from '../../../../components/layout';
import API from '../../../../libs/axios';
import {toast} from 'react-toastify';
import {Container, Breadcrumb, Card, Row, Col, Button, Form} from 'react-bootstrap';
import { FaTrash, FaPencilAlt} from 'react-icons/fa';
import { Formik } from 'formik';
import * as yup from 'yup';
import Loader from 'react-loader';
import DataTable from 'react-data-table-component';
import styled from 'styled-components';
import Dialog from 'react-bootstrap-dialog';

var options = {lines: 13,length: 20,width: 10,radius: 30,scale: 0.35,corners: 1,color: '#fff',opacity: 0.25,rotate: 0,direction: 1,speed: 1,trail: 60,fps: 20,zIndex: 2e9,top: '50%',left: '50%',shadow: false,hwaccel: false,position: 'absolute'};
class Category extends Component {
    constructor(props) {
        super(props)
        this.state = {
            Category: [],
            url: ImagesUrl(),
            loading: true 
        }

    }

    componentDidMount = () => {
        API.GetCategory().then(res => {
          if (res.data.length > 0) {
            setTimeout(() => this.setState({
                Category: res.data,
                loading: false
            }), 100);
          } else {
            this.setState({
                error: "No Data Found",
                loading: false
            })
        }
        }).catch(err => {
          console.log(err.response)
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
          name: 'Kategori',
          selector: 'name',
          sortable: true
        },
        {
          name: 'Aksi',
          sortable: false,
          cell: row => <><Link href={'/admin/blog/category/edit/'+row.id} passHref><Button size="sm" title="Edit" alt="Edit"><FaPencilAlt/></Button></Link>&nbsp;
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
                      API.DeleteCategory(row.id).then(res => {
                        if (res.status === 1) {
                            toast.success("Hapus data berhasil", {position: "top-center"});
                            setTimeout(() => {
                            Router.push('/admin/blog/category');
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
      <Link href="/admin/blog/category/create" passHref><Button variant="primary" style={{ position: 'absolute', left: '0', marginLeft: '15px'}}>Tambah Kategori</Button></Link>
        <TextField id="search" type="text" placeholder="Filter By Judul" aria-label="Search Input" value={filterText} onChange={onFilter} />
        <ClearButton variant="secondary" type="button" onClick={onClear}>X</ClearButton>
      </>
    );
    
    const BasicTable = () => {
      const [filterText, setFilterText] = useState('');
      const [resetPaginationToggle, setResetPaginationToggle] = useState(false);
      const filteredItems = this.state.Category.filter(item => item.name && item.name.toLowerCase().includes(filterText.toLowerCase()) 
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
          title="Kategori Blog"
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
                    <title>Kategori Blog - {siteTitle}</title>
                </Head>
                <Container fluid>
                <Breadcrumb className="my-3">
                <Link href="/admin" passHref><Breadcrumb.Item >Home</Breadcrumb.Item></Link>
                <Link href="/admin/blog" passHref><Breadcrumb.Item >Blog</Breadcrumb.Item></Link>
                <Breadcrumb.Item active>Kategori</Breadcrumb.Item>
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



export default Category;