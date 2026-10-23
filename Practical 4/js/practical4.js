const themeButton = document.getElementById('themeBtn');
const savedTheme = localStorage.getItem('studenthub-theme');
if (savedTheme === 'dark') document.body.classList.add('dark');
themeButton.addEventListener('click', () => {
  document.body.classList.toggle('dark');
  localStorage.setItem('studenthub-theme', document.body.classList.contains('dark') ? 'dark' : 'light');
});
document.querySelectorAll('.faq').forEach(button => {
  button.addEventListener('click', () => {
    const answer = button.nextElementSibling;
    answer.classList.toggle('show');
    button.setAttribute('aria-expanded', answer.classList.contains('show'));
  });
});
const slides = [...document.querySelectorAll('.slide')];
let currentSlide = 0;
function showSlide(index) {
  slides[currentSlide].classList.remove('active');
  currentSlide = (index + slides.length) % slides.length;
  slides[currentSlide].classList.add('active');
}
document.getElementById('next').addEventListener('click', () => showSlide(currentSlide + 1));
document.getElementById('prev').addEventListener('click', () => showSlide(currentSlide - 1));
const modal = document.getElementById('modal');
document.getElementById('modalBtn').addEventListener('click', () => modal.style.display = 'flex');
document.getElementById('closeModal').addEventListener('click', () => modal.style.display = 'none');
modal.addEventListener('click', event => { if (event.target === modal) modal.style.display = 'none'; });
document.getElementById('menuBtn').addEventListener('click', () => {
  const menu = document.getElementById('menu');
  const hidden = menu.style.display === 'none';
  menu.style.display = hidden ? 'flex' : 'none';
  document.getElementById('menuBtn').setAttribute('aria-expanded', String(hidden));
});
