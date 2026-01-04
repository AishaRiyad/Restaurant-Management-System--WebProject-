let menu = document.querySelector('#menub');
let navv = document.querySelector('.navv');
// const formpp=document.querySelector(".p-form");
// const loginsign=document.querySelectorAll(" .form-box .buttom-link a");

menu.onclick = () => {
    menu.classList.toggle('fa-times');
    navv.classList.toggle('active');
}

window.onscroll = () => {
    menu.classList.remove('fa-times');
    navv.classList.remove('active');
}


// loginsign.forEach(link =>{
//     link.addEventListener("click", (e) => {
//        e.preventDefault();
//        formpp.classList[link.id==="signup-link" ? 'add' : 'remove']("show-signup");
//     });
// });



