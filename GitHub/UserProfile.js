// Default cover
const cover = document.getElementById('cover');
cover.style.backgroundImage = "url('https://images.unsplash.com/photo-1503264116251-35a269479413?auto=format&fit=crop&w=1500&q=60')";

const coverInput = document.getElementById('coverInput');
const resetCover = document.getElementById('resetCover');
let coverPos = { x: 50, y: 50 };

function setCoverPosition(x, y) {
  coverPos = { x, y };
  cover.style.backgroundPosition = `${x}% ${y}%`;
}

// Upload cover
coverInput.addEventListener('change', e => {
  const f = e.target.files[0];
  if (!f) return;
  const url = URL.createObjectURL(f);
  cover.style.backgroundImage = `url('${url}')`;
  setCoverPosition(50, 50);
});

// Reset cover
resetCover.addEventListener('click', () => {
  cover.style.backgroundImage = "url('https://images.unsplash.com/photo-1503264116251-35a269479413?auto=format&fit=crop&w=1500&q=60')";
  setCoverPosition(50, 50);
});

// Drag reposition cover
let dragging = false;
let lastX = 0, lastY = 0;

cover.addEventListener('pointerdown', e => {
  dragging = true;
  cover.classList.add('dragging');
  lastX = e.clientX;
  lastY = e.clientY;
});

cover.addEventListener('pointermove', e => {
  if (!dragging) return;

  const dx = e.clientX - lastX;
  const dy = e.clientY - lastY;
  lastX = e.clientX;
  lastY = e.clientY;

  const rect = cover.getBoundingClientRect();
  const pxToPctX = 100 / rect.width;
  const pxToPctY = 100 / rect.height;

  let newX = coverPos.x + dx * pxToPctX;
  let newY = coverPos.y + dy * pxToPctY;

  newX = Math.max(0, Math.min(100, newX));
  newY = Math.max(0, Math.min(100, newY));

  setCoverPosition(newX, newY);
});

cover.addEventListener('pointerup', () => {
  dragging = false;
  cover.classList.remove('dragging');
});

// Avatar upload
const avatarInput = document.getElementById('avatarInput');
const avatarImg = document.getElementById('avatarImg');

avatarInput.addEventListener('change', e => {
  const f = e.target.files[0];
  if (!f) return;
  const url = URL.createObjecbbtURL(f); 
  avatarImg.src = url;
});
