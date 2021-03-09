import Axios from 'axios'

const RootPath = "http://localhost/itshopid/api/"

// Authorization
// key = blog123
// Gunakan https://www.base64decode.org untuk melakukan encode key diatas menjadi format base64
var key = new Buffer.from('aW5vdmFzaTIwMjE=', 'base64')
const ApiKey = key.toString();
const config = { headers: { 'X-API-KEY': `${ApiKey}` } };

const GET = (path) => {
    const promise = new Promise((resolve,reject) => {
        Axios.get(RootPath+path, config).then(res => {
            resolve(res.data)
        }).catch(err => {
            reject(err)
        })
    })
    return promise
}

const GET_ID = (path,id) => {
    const promise = new Promise((resolve,reject) => {
        Axios.get(RootPath+path+id, config).then(res => {
            resolve(res.data)
        }).catch(err => {
            reject(err)
        })
    })
    return promise
}

const GET_BY_ID = (path,data) =>{
    const promise = new Promise((resolve,reject)=>{
         Axios.get(RootPath+path+data, config).then(res=>{
             resolve(res.data)
         },err=>{
            console.log(err.response); 
            return err.response;
         })
    })
    return promise
 }

const POST = (path,data) => {
    const promise = new Promise((resolve,reject) => {
        Axios.post(RootPath+path,data, config).then(res => {
            resolve(res.data)
        }).catch(err => {
            reject(err)
        })
    })
    return promise
}

const PUT = (path,data) => {
    const promise = new Promise((resolve,reject) => {
        Axios.put(RootPath+path,data, config).then(res => {
            resolve(res.data)
        }).catch(err => {
            reject(err)
        })
    })
    return promise
}

const DELETE = (path,data) => {
    const promise = new Promise((resolve,reject) => {
        Axios.delete(RootPath+path+data, config).then(res => {
            resolve(res.data)
        }).catch(err => {
            reject(err)
        })
    })
    return promise
}

const SEARCH = (path,data) => {
    const promise = new Promise((resolve,reject) => {
        Axios.get(RootPath+path+data, config).then(res => {
            resolve(res.data)
        }).catch(er => {
            reject(er)
        })
    })
    return promise
}

const POST_FOTO = (path,data,name) => {
    const promise = new Promise((resolve,reject)=>{
        const formdata = new FormData()
        formdata.append('foto',data,name)
        Axios.post(RootPath+path, formdata, config).then(res=>{
           resolve(res.data.status)
       },(err)=>{
           reject(err)
       })
    })
    return promise
}

const PostLogin = (data) => POST('Login',data);
const GetBlog = () => GET('Blogs');
const GetBlogId = (data) => GET_ID('Blogs?id=',data)
const PostBlog = (data) => POST('Blogs',data);
const PutBlog = (data) => PUT('Blogs',data);
const PutBlogCategory = (data) => PUT('BlogsCategory',data);
const DeleteBlog = (id) => DELETE('Blogs/index_delete?id=',id);
const PutBlogImage = (data) => PUT('BlogsImage',data);
const GetSetting = () => GET('Settings');
const PutSetting = (data) => PUT('Settings',data);
const GetUser = () => GET('Users');
const GetUserId = (data) => GET_ID('Users?id=',data)
const PostUser = (data) => POST('Users',data);
const PutUser = (data) => PUT('Users',data);
const PutUserPass = (data) => PUT('UserPass',data);
const DeleteUser = (id) => DELETE('Users/index_delete?id=',id);
const GetSlideshow = () => GET('Slideshow');
const GetSlideshowId = (data) => GET_ID('Slideshow?id=',data)
const PostSlideshow = (data) => POST('Slideshow',data);
const PutSlideshow = (data) => PUT('Slideshow',data);
const DeleteSlideshow = (id) => DELETE('Slideshow/index_delete?id=',id);
const PutSlideshowImage = (data) => PUT('SlideshowImage',data);
const GetCategory = () => GET('Category');
const GetCategoryId = (data) => GET_ID('Category?id=',data)
const PostCategory = (data) => POST('Category',data);
const PutCategory = (data) => PUT('Category',data);
const DeleteCategory = (id) => DELETE('Category/index_delete?id=',id);
const PostFoto = (data,name) => POST_FOTO('ImageUpload',data,name);
const CountBlog = () => GET('CountBlogs');
const CountCategory = () => GET('CountCategory');
const SearchBlog = (data) => SEARCH('Search?id=',data);
const GetComment = () => GET('Comments');
const GetCommentId = (data) => GET_ID('Comments?id=',data)
const PostComment = (data) => POST('Comments',data);
const PutComment = (data) => PUT('Comments',data);
const CountComment = () => GET('CountComment');
const GetTag = (data) => GET_ID('Tags?category=',data);
const GetCatalog = () => GET('Catalog');

const API = {
    PostLogin,
    GetBlog,
    GetBlogId,
    PostBlog,
    PutBlog,
    PutBlogCategory,
    DeleteBlog,
    PutBlogImage,
    GetSetting,
    PutSetting,
    GetUser,
    GetUserId,
    PostUser,
    PutUser,
    PutUserPass,
    DeleteUser,
    GetSlideshow,
    GetSlideshowId,
    PostSlideshow,
    PutSlideshow,
    DeleteSlideshow,
    PutSlideshowImage,
    GetCategory,
    GetCategoryId,
    PostCategory,
    PutCategory,
    DeleteCategory,
    PostFoto,
    CountBlog,
    CountCategory,
    SearchBlog,
    GetComment,
    GetCommentId,
    PostComment,
    PutComment,
    CountComment,
    GetTag,
    GetCatalog,
}

export default API