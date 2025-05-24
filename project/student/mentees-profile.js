document.addEventListener('DOMContentLoaded', () => {
  fetchStudentInfo();
  fetchFamilyInfo();
  fetchAcademicInfo();
});

function toggleNav() {
  const navItems = document.getElementById('nav-items');
  navItems.classList.toggle('show');
}

function toggleDropdown() {
  const dropdown = document.getElementById('notification-dropdown');
  dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Optional: Close dropdown when clicking outside
window.onclick = function(e) {
  if (!e.target.closest('.notification-wrapper')) {
    document.getElementById('notification-dropdown').style.display = 'none';
  }
}


async function fetchStudentInfo() {
  try {
    const response = await fetch('/api/student-info'); // Adjust endpoint as needed
    const data = await response.json();

    document.getElementById('name').value = data.name;
    document.getElementById('usn').value = data.usn;
    document.getElementById('dob').value = data.dob;
    document.getElementById('sslc').value = data.sslc;
    document.getElementById('puc').value = data.puc;
    document.getElementById('email').value = data.email;
    document.getElementById('place').value = data.place;
    document.getElementById('hobbies').value = data.hobbies;

    // Optional: Set profile picture if available
    if (data.profilePicUrl) {
      document.querySelector('.profile-image').src = data.profilePicUrl;
    }
  } catch (error) {
    console.error('Error fetching student info:', error);
  }
}

async function fetchFamilyInfo() {
  try {
    const response = await fetch('/api/family-info'); // Adjust endpoint as needed
    const data = await response.json();

    document.getElementById('fatherName').value = data.fatherName;
    document.getElementById('motherName').value = data.motherName;
    document.getElementById('fatherJob').value = data.fatherJob;
    document.getElementById('motherJob').value = data.motherJob;
    document.getElementById('familyAddress').value = data.address;
    document.getElementById('fatherPhone').value = data.fatherPhone;
    document.getElementById('motherPhone').value = data.motherPhone;
  } catch (error) {
    console.error('Error fetching family info:', error);
  }
}

async function fetchAcademicInfo() {
  try {
    const response = await fetch('/api/academic-info'); // Adjust endpoint as needed
    const academicData = await response.json();

    const tbody = document.querySelector('#academic-info tbody');
    tbody.innerHTML = ''; // Clear any existing rows

    academicData.forEach(subject => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td><input type="text" value="${subject.courseCode}" readonly></td>
        <td><input type="text" value="${subject.courseName}" readonly></td>
        <td><input type="number" value="${subject.attendance}" readonly></td>
        <td><input type="number" value="${subject.mse1}" readonly></td>
        <td><input type="number" value="${subject.mse2}" readonly></td>
        <td><input type="number" value="${subject.addMSE}" readonly></td>
        <td><input type="number" value="${subject.avg}" readonly></td>
        <td><input type="number" value="${subject.see}" readonly></td>
        <td><input type="text" value="${subject.grade}" readonly></td>
        <td><input type="number" value="${subject.gp}" readonly></td>
      `;
      tbody.appendChild(row);
    });
  } catch (error) {
    console.error('Error fetching academic info:', error);
  }
}
