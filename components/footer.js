import React,{Component} from 'react';
import { Container, Row, Col} from 'react-bootstrap';
 
class Footer extends Component{
    render(){
     
        return(  
               
            <div className="text-white border-0 py-5" style={{backgroundColor:'#212529'}}>
              <Container>
            <Row>

                <Col md={12}>
                <h3>Footer</h3>
            
                </Col>

            </Row>
           
            <div className="text-white mt-3">© {(new Date().getFullYear())} {this.props.setting.company}.</div>
            </Container>
            </div>


        )
    }
}

export default Footer