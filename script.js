// Mobile menu
const btn = document.querySelector('.menu-btn');
const menu = document.querySelector('nav ul');
if (btn) btn.addEventListener('click', () => menu.classList.toggle('open'));

// Gallery filter + lightbox
const filterBtns = document.querySelectorAll('.filters button');
const tiles = document.querySelectorAll('.tile');
filterBtns.forEach(b => b.addEventListener('click', () => {
  filterBtns.forEach(x => x.classList.remove('on'));
  b.classList.add('on');
  const f = b.dataset.f;
  tiles.forEach(t => t.style.display = (f === 'all' || t.dataset.c === f) ? '' : 'none');
}));
const lb = document.querySelector('.lightbox');
if (lb) {
  tiles.forEach(t => t.addEventListener('click', () => {
    lb.querySelector('div').textContent = t.dataset.icon;
    lb.querySelector('p').textContent = t.querySelector('.cap').textContent;
    lb.classList.add('open');
  }));
  lb.addEventListener('click', () => lb.classList.remove('open'));
}

// Contact form -> opens email app with details filled in
const form = document.getElementById('enquiry');
if (form) form.addEventListener('submit', e => {
  e.preventDefault();
  const d = new FormData(form);
  const body = `Name: ${d.get('name')}\nPhone: ${d.get('phone')}\nService: ${d.get('service')}\n\n${d.get('message')}`;
  window.location.href = 'mailto:prashanthhomesolutions@gmail.com?subject=' +
    encodeURIComponent('Enquiry: ' + d.get('service')) + '&body=' + encodeURIComponent(body);
  document.querySelector('.form-msg').style.display = 'block';
});
