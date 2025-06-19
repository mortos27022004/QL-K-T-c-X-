const hamburger = document.querySelector(".toggle-btn");
const toggler = document.querySelector("#icon");
hamburger.addEventListener("click",function(){
  document.querySelector("#sidebar").classList.toggle("expand");
  if (toggler.textContent === "chevron_right") {
    toggler.textContent = "chevron_left"; // Icon trái
  } else {
    toggler.textContent = "chevron_right"; // Icon phải
  }
})
