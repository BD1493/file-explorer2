document.addEventListener('DOMContentLoaded', () => {

    // Color-code file list
    document.querySelectorAll('ul li').forEach((li,index)=>{
        li.style.backgroundColor=`hsl(${index*35%360},70%,90%)`;
        li.style.padding='5px 10px';
        li.style.margin='3px 0';
        li.style.borderRadius='5px';
        li.style.listStyle='none';
    });

    // Modal for share
    const modal=document.getElementById('modal');
    const modalContent=document.getElementById('modal-body');
    const modalClose=document.getElementById('modal-close');
    function openModal(html){modalContent.innerHTML=html;modal.classList.remove('hidden');}
    modalClose.addEventListener('click',()=>modal.classList.add('hidden'));
    modal.addEventListener('click',e=>{if(e.target===modal) modal.classList.add('hidden');});

    document.querySelectorAll('.share-btn').forEach(btn=>{
        btn.addEventListener('click',e=>{
            const li=e.target.closest('li');
            openModal(`<iframe src="share.php?id=${li.dataset.fileId}" style="width:100%;height:300px;border:none;"></iframe>`);
        });
    });

    document.querySelectorAll('.edit-btn').forEach(btn=>{
        btn.addEventListener('click',e=>{
            const li=e.target.closest('li');
            window.location.href=`edit.php?id=${li.dataset.fileId}`;
        });
    });

});
