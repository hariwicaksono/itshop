import React, { Component } from 'react';
//import {Redirect,Link} from 'react-router-dom'
import API from '../lib/axios';
import SearchResult from './searchResult';
//import { NotificationManager } from 'react-notifications'
import { Form, Button, Spinner, InputGroup  } from 'react-bootstrap';
import { FaSearch } from 'react-icons/fa';
//import Form from 'react-formal'
//import * as yup from 'yup'

//const schema = yup.object({
    //query: yup.string().required(),
  //}); 

class SearchForm extends Component {
    constructor(props) {
        super(props)
        this.state = {
            query: '',
            results: [],
            loading: false
        }
        this.handlerChange = this.handlerChange.bind(this)
        this.handlerSubmit = this.handlerSubmit.bind(this)
        
    }

    handlerChange = (event) => {
        this.setState({
            [event.target.name]: event.target.value
        })
    }

    handlerSubmit = (e) => {
        e.preventDefault();
        this.setState({ loading: true });
        const query=this.state.query;
        API.SearchBlog(query).then(res=>{
            //console.log(res)
            setTimeout(() => this.setState({
              results: res.data,
              loading: false,
            }), 100);
        });  
    }

    
    render() {

        return (
           
                <Form className="d-flex w-25 me-3" onSubmit={this.handlerSubmit}>
                    <InputGroup>
                    <Form.Control type="text" name="query" placeholder="Search" onChange={this.handlerChange} required/>
            
                    <Button variant="light" type="submit">
                    {
                        this.state.loading
                        ?
                        <><Spinner as="span" animation="grow" size="sm"  role="status" aria-hidden="true" /></>
                        :   
                    <><FaSearch size="1.2em" /></>}
                    </Button>
                    </InputGroup>
                {this.state.results.length > 0 && (
                
                <SearchResult data={this.state.results} />

               )}

               
                </Form>   
        )
    }

}

export default SearchForm;