document.addEventListener('DOMContentLoaded', () => {
  const studentBtn = document.getElementById('showStudent');
  const mentorBtn = document.getElementById('showMentor');
  const studentForm = document.getElementById('studentForm');
  const mentorForm = document.getElementById('mentorForm');

  studentBtn.addEventListener('click', () => {
    studentForm.classList.add('active');
    mentorForm.classList.remove('active');
    studentBtn.classList.add('active');
    mentorBtn.classList.remove('active');
  });

  mentorBtn.addEventListener('click', () => {
    mentorForm.classList.add('active');
    studentForm.classList.remove('active');
    mentorBtn.classList.add('active');
    studentBtn.classList.remove('active');
  });
});
