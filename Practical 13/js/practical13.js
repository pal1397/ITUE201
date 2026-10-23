const api='api/students.php', form=document.getElementById('studentForm');
const statusBox=document.getElementById('apiStatus');
async function request(url,options={}) {
 const response=await fetch(url,{...options,headers:{'Content-Type':'application/json',...(options.headers||{})}});
 const data=await response.json(); if(!response.ok) throw new Error(data.error||'Request failed'); return data;
}
async function loadStudents(){
 try{const rows=await request(api);const body=document.getElementById('studentRows');body.replaceChildren();
 rows.forEach(s=>{const tr=document.createElement('tr');[s.id,s.name,s.email,s.course,s.year].forEach(v=>{const td=document.createElement('td');td.textContent=v;tr.appendChild(td);});
 const td=document.createElement('td');const edit=document.createElement('button');edit.textContent='Edit';edit.onclick=()=>fillForm(s);
 const del=document.createElement('button');del.textContent='Delete';del.onclick=()=>deleteStudent(s.id);td.append(edit,del);tr.appendChild(td);body.appendChild(tr);});
 statusBox.textContent='Records loaded successfully.';}catch(e){statusBox.textContent=e.message+' (Login first; admin role is required for changes.)';}
}
function fillForm(s){studentId.value=s.id;studentName.value=s.name;studentEmail.value=s.email;studentCourse.value=s.course;studentYear.value=s.year;}
form.addEventListener('submit',async e=>{e.preventDefault();const id=studentId.value;const payload={name:studentName.value,email:studentEmail.value,course:studentCourse.value,year:studentYear.value};
 try{await request(api,{method:id?'PUT':'POST',body:JSON.stringify(id?{...payload,id:Number(id)}:payload)});form.reset();studentId.value='';statusBox.textContent='Saved successfully.';loadStudents();}catch(err){statusBox.textContent=err.message;}});
async function deleteStudent(id){if(!confirm('Delete this student?'))return;try{await request(api,{method:'DELETE',body:JSON.stringify({id})});statusBox.textContent='Deleted successfully.';loadStudents();}catch(e){statusBox.textContent=e.message;}}
document.getElementById('clearForm').onclick=()=>{form.reset();studentId.value='';};
loadStudents();
