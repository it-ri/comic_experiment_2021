var count=0;
document.addEventListener("DOMContentLoaded", function(){
  document.getElementById("btn2").addEventListener('click', function() {
    count++;
    target = document.getElementById("output");
    target.innerHTML = document.getElementById("btn2").value;
  });

  document.getElementById("btn").addEventListener('click', function() {
    count++;
    target = document.getElementById("output");
    target.innerHTML = document.getElementById("btn").value;
  });

}, false);