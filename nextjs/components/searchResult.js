import React, {useState} from 'react';
import Link from 'next/link';
//import {X} from 'react-bootstrap-icons'

const SearchResults = (props) => {
  
  const options = props.data.map(r => (
  <div className="text-dark" key={r.id}>
    <h6><Link href={"/blog/posts/"+r.id} passHref><>{r.title}</></Link></h6>
  </div>
   
  ))

  const [show, setShow] = useState(true);

  if (show) {
    return (
      <div id="searchResult" className="col-md-5" style={{paddingLeft:0}}>
      <div className="card card-body" style={{maxHeight:'250px',overflowY: 'auto'}} >
      <div className="d-flex justify-content-end">
          <a href="#/" className="text-dark" >
            X
          </a>
        </div>
       {options}

      </div>
      
   </div>
    );
    
  }
  return true;
}

export default SearchResults