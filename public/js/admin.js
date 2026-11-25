const NAV=[['dashboard','tachometer','Tổng quan'],['products','shopping-bag','Sản phẩm & Tồn kho'],['categories','tags','Danh mục & Size'],['orders','file-text-o','Đơn hàng'],['reviews','star','Đánh giá'],['users','users','Người dùng']];
const P=document.body.dataset.page;
document.body.insertAdjacentHTML('afterbegin','<aside class="sidebar" id="sidebar"><div class="brand"><b>Huce</b>store Admin</div>'+NAV.map(n=>`<a href="${n[0]}.html" class="${n[0]==P?'on':''}"><i class="fa fa-${n[1]}"></i> ${n[2]}</a>`).join('')+'<a href="../index.html" style="margin-top:20px"><i class="fa fa-external-link"></i> Xem website</a></aside><div class="toast" id="toast"></div>');
const tb=document.getElementById('topbar');
if(tb)tb.outerHTML=`<div class="topbar"><div><button class="burger" onclick="sidebar.classList.toggle('open')">&#9776;</button> <h1 style="display:inline">${document.body.dataset.title}</h1></div><div><i class="fa fa-user-circle"></i> admin@hucestore.com &nbsp;<a href="login.html" class="btn sm dark">Đăng xuất</a></div></div>`;
const $=id=>document.getElementById(id);
function openM(id){$(id).style.display='block'}function closeM(id){$(id).style.display='none'}
function toast(m){const t=$('toast');t.textContent=m;t.style.display='block';setTimeout(()=>t.style.display='none',2600)}
const money=n=>n.toLocaleString('vi-VN')+'₫';
