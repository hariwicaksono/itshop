import Axios from 'axios'
 
const RootPath = "http://mywebci4-restapi.test/"

// Authorization
// key = itshop123
// Gunakan https://www.base64decode.org untuk melakukan encode key diatas menjadi format base64
var key = new Buffer.from('aXRzaG9wMTIz', 'base64')
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

const PostLogin = (data) => POST('auth',data);
const GetBlog = () => GET('blog');
const GetBlogId = (data) => GET_ID('blog?id=',data)
const PostBlog = (data) => POST('blog',data);
const PutBlog = (data) => PUT('blog',data);
const PutBlogCategory = (data) => PUT('blogcategory',data);
const DeleteBlog = (id) => DELETE('blog/delete/',id);
const PutBlogImage = (data) => PUT('blogimage',data);
const GetSetting = () => GET('setting');
const PutSetting = (data) => PUT('setting',data);
const PutSettingLanding = (data) => PUT('settinglanding',data);
const GetUser = () => GET('user');
const GetUserId = (data) => GET_ID('user?id=',data)
const PostUser = (data) => POST('user',data);
const PutUser = (data) => PUT('user',data);
const PutUserPass = (data) => PUT('userpassword',data);
const DeleteUser = (id) => DELETE('user/delete/',id);
const GetSlideshow = () => GET('slideshow');
const GetSlideshowId = (data) => GET_ID('slideshow?id=',data)
const PostSlideshow = (data) => POST('slideshow',data);
const PutSlideshow = (data) => PUT('slideshow',data);
const DeleteSlideshow = (id) => DELETE('slideshow/delete/',id);
const PutSlideshowImage = (data) => PUT('slideshowimage',data);
const GetCategory = () => GET('category');
const GetCategoryId = (data) => GET_ID('category?id=',data)
const PostCategory = (data) => POST('category',data);
const PutCategory = (data) => PUT('category',data);
const DeleteCategory = (id) => DELETE('category/delete/',id);
const PostFoto = (data,name) => POST_FOTO('imageupload',data,name);
const CountBlog = () => GET('countblog');
const CountCategory = () => GET('countcategory');
const SearchBlog = (data) => SEARCH('search?id=',data);
const GetComment = () => GET('comment');
const GetCommentId = (data) => GET_ID('comment?id=',data)
const PostComment = (data) => POST('comment',data);
const PutComment = (data) => PUT('comment',data);
const CountComment = () => GET('countcomment');
const GetTag = (data) => GET_ID('tag?category=',data);
const GetMenu = () => GET('menu');
const GetProduct = () => GET('product');
const CountProduct = () => GET('countproduct');

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
    PutSettingLanding,
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
    GetMenu,
    GetProduct,
    CountProduct
}

export default API