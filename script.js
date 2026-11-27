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

// Enquiry forms.
// A form with an "action" (contact.html) is sent by PHP; one without opens the email app.
const form = document.getElementById('enquiry');
if (form) form.addEventListener('submit', async e => {
  e.preventDefault();
  const d = new FormData(form);
  const msg = form.querySelector('.form-msg');
  const url = form.getAttribute('action');

  if (!url) {
    const body = `Name: ${d.get('name')}\nPhone: ${d.get('phone')}\nService: ${d.get('service')}\n\n${d.get('message')}`;
    window.location.href = 'mailto:prashanthhomesolutions02@gmail.com?subject=' +
      encodeURIComponent('Enquiry: ' + d.get('service')) + '&body=' + encodeURIComponent(body);
    msg.style.display = 'block';
    return;
  }

  const button = form.querySelector('button[type="submit"]');
  const label = button.textContent;
  button.disabled = true;
  button.textContent = 'Sending...';
  msg.style.display = 'none';

  let result;
  try {
    const res = await fetch(url, { method: 'POST', body: d, headers: { Accept: 'application/json' } });
    result = await res.json();
  } catch (err) {
    result = { ok: false, message: 'Sorry, your enquiry could not be sent. Please call us on 9380989804 or message us on WhatsApp.' };
  }

  msg.textContent = result.message;
  msg.classList.toggle('error', !result.ok);
  msg.style.display = 'block';
  if (result.ok) form.reset();
  button.disabled = false;
  button.textContent = label;
});
