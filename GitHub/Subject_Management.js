AOS.init({duration:700,once:true});
    document.getElementById('year').textContent = new Date().getFullYear();
   
    function handleContact(e){
      e.preventDefault();
      const f = e.target;
      const data = {name:f.name.value, email:f.email.value, message:f.message.value};
    
      const subject = encodeURIComponent('Contact from portfolio: ' + data.name);
      const body = encodeURIComponent(data.message + '\n\n' + data.email);
      window.location.href = `mailto:youremail@example.com?subject=${subject}&body=${body}`;
    }

    document.querySelectorAll('button, a').forEach(el=>el.setAttribute('tabindex','0'));
function toggleMenu() {
  document.body.classList.toggle("open");
}

const grid = document.getElementById('projectsGrid');
let scrollAmount = 0;
const projectWidth = 320; 

function scrollProjects(direction) {
  if(direction === 'left') {
    scrollAmount -= projectWidth;
    if(scrollAmount < 0) scrollAmount = 0; 
  } else {
    const maxScroll = grid.scrollWidth - grid.parentElement.offsetWidth;
    scrollAmount += projectWidth;
    if(scrollAmount > maxScroll) scrollAmount = maxScroll;
  }
  grid.style.transform = `translateX(-${scrollAmount}px)`;
}

window.addEventListener('load', () => {
    setTimeout(() => {
      document.getElementById('intro').style.display = 'none';
      document.getElementById('main').classList.add('show');
    }, 3000); 
});