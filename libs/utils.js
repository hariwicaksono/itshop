import { toast } from 'react-toastify';
import Router from 'next/router';

const TOKEN_KEY1 = 'isLogin';
const TOKEN_KEY2 = 'isAdmin';

export const login = () => {
    localStorage.setItem(TOKEN_KEY1, 'TestLogin');
}

export const logout = () => {
    (localStorage.removeItem(TOKEN_KEY1) || localStorage.removeItem(TOKEN_KEY2));
    toast.warn("Berhasil keluar sistem", {position: "top-center"}); 
    setTimeout(()=>{
        Router.reload();
      },2000);
}

export const isLogin = () => {
    if (localStorage.getItem(TOKEN_KEY1)) {
        return true;
    }

    return false;
}

export const isAdmin = () => {
    if (localStorage.getItem(TOKEN_KEY2)) {
        return true;
    }

    return false;
}