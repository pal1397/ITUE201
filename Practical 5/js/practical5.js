const form = document.getElementById('registrationForm');
const fields = ['name','email','mobile','password','confirmPassword','course','year','gender'];
function showError(id, message) { document.getElementById(id + 'Error').textContent = message; }
form.addEventListener('submit', event => {
  event.preventDefault();
  fields.forEach(id => showError(id, ''));
  showError('terms', '');
  document.getElementById('formMessage').textContent = '';
  const values = Object.fromEntries(fields.map(id => [id, document.getElementById(id).value.trim()]));
  let valid = true;
  const check = (id, condition, message) => { if (!condition) { showError(id, message); valid = false; } };
  check('name', /^[A-Za-z ]{2,50}$/.test(values.name), 'Enter a valid name (letters and spaces only).');
  check('email', /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(values.email), 'Enter a valid email address.');
  check('mobile', /^[6-9]\d{9}$/.test(values.mobile), 'Enter a valid 10-digit mobile number.');
  check('password', /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/.test(values.password), 'Password does not meet the required strength.');
  check('confirmPassword', values.password === document.getElementById('confirmPassword').value, 'Passwords do not match.');
  ['course','year','gender'].forEach(id => check(id, values[id] !== '', 'Please select ' + id + '.'));
  if (!document.getElementById('terms').checked) { showError('terms', 'Please accept the terms.'); valid = false; }
  if (valid) document.getElementById('formMessage').innerHTML = '<span class="success">All fields are valid. Frontend validation successful.</span>';
});
