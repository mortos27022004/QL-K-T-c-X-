const toggler = document.querySelector(".toggler-btn");
toggler.addEventListener("click",function(){
    document.querySelector("#sidebar").classList.toggle("collapsed");
});

const items = document.querySelectorAll('#sidebar .sidebar-nav a');

items.forEach(item => {
  item.addEventListener('click', function () {
    // Bỏ active khỏi tất cả item
    items.forEach(i => i.classList.remove('active'));
    // Thêm active cho item vừa click
    this.classList.add('active');
  });
});