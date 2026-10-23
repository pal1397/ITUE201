let records = [], page = 1;
const pageSize = 6;
const list = document.getElementById('eventList');
const dataFiles = { events: 'data/events.json', students: 'data/students.json', faqs: 'data/faqs.json' };
async function loadData() {
  const type = document.getElementById('dataset').value;
  document.getElementById('status').textContent = 'Loading ' + type + '...';
  try {
    const response = await fetch(dataFiles[type]);
    if (!response.ok) throw new Error('Unable to load ' + dataFiles[type]);
    records = await response.json(); page = 1;
    const category = document.getElementById('category');
    category.replaceChildren(new Option('All categories', ''));
    const key = type === 'events' ? 'category' : (type === 'students' ? 'course' : 'category');
    const values = [...new Set(records.map(item => item[key]).filter(Boolean))].sort();
    values.forEach(value => category.appendChild(new Option(value, value)));
    render();
  } catch (error) { document.getElementById('status').textContent = error.message + '. Run this folder through a local web server.'; }
}
function render() {
  const type = document.getElementById('dataset').value;
  const term = document.getElementById('search').value.toLowerCase();
  const category = document.getElementById('category').value;
  const sort = document.getElementById('sort').value;
  const categoryKey = type === 'events' ? 'category' : (type === 'students' ? 'course' : 'category');
  const filtered = records.filter(item => Object.values(item).join(' ').toLowerCase().includes(term) && (!category || item[categoryKey] === category));
  filtered.sort((a,b) => String(a[sort] ?? a.title ?? a.name ?? a.question ?? '').localeCompare(String(b[sort] ?? b.title ?? b.name ?? b.question ?? '')));
  const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize)); page = Math.min(page, totalPages);
  list.replaceChildren();
  filtered.slice((page-1)*pageSize, page*pageSize).forEach(item => {
    const card = document.createElement('article'); card.className = 'card';
    const title = document.createElement('h3'); title.textContent = item.title || item.name || item.question || 'Record';
    const detail = document.createElement('p'); detail.className = 'muted';
    detail.textContent = Object.entries(item).filter(([key]) => key !== 'title' && key !== 'name' && key !== 'question').map(([key,value]) => key + ': ' + value).join(' | ') || item.answer || '';
    card.append(title, detail); list.appendChild(card);
  });
  document.getElementById('status').textContent = filtered.length ? `${filtered.length} record(s) found` : 'No records found.';
  document.getElementById('pageInfo').textContent = `Page ${page} of ${totalPages}`;
  document.getElementById('previous').disabled = page === 1; document.getElementById('next').disabled = page === totalPages;
}
document.getElementById('dataset').addEventListener('change', loadData);
['search','category','sort'].forEach(id => document.getElementById(id).addEventListener('input', () => { page=1; render(); }));
document.getElementById('previous').addEventListener('click', () => { page--; render(); });
document.getElementById('next').addEventListener('click', () => { page++; render(); });
loadData();
