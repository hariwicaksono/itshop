import React,{Component} from 'react';
import { Container, Row, Col} from 'react-bootstrap';
 
class Footer extends Component{
    render(){
     
        return(  
               
        <footer className="footer border-0 bg-secondary text-dark py-3">
            <Container>
           
            <div className="text-white mt-3">© {(new Date().getFullYear())} {this.props.setting.company}. Aplikasi Web Company Profile dan Blog dengan React Next.js dan CodeIgniter 4</div>
            </Container>
        </footer>


        )
    }
}

export default Footer;