// server.js (Backend logic)

const express = require('express');
const app = express();
const port = 3000;

// Simulated mentor data (from DB in a real app)
const mentorData = {
  name: "John Doe",
  email: "john.doe@example.com",
  phone: "123-456-7890",
  place: "Tech Company",
  hobbies: "Reading, Mentoring",
  image: "/images/mentor-image.jpg" // Image path for static folder
};

// Serve the mentor data as JSON at the '/api/mentor' endpoint
app.get('/api/mentor', (req, res) => {
  res.json(mentorData);
});

// Serve static files like images, CSS, JS
app.use(express.static('public'));

// Start server
app.listen(port, () => {
  console.log(`Server is running on http://localhost:${port}`);
});

// mentor-profile.js (Frontend logic)

fetch('/api/mentor')
  .then(response => response.json())
  .then(data => {
    // Populate the page with the mentor's data
    document.getElementById('mentor-name').innerText = data.name;
    document.getElementById('mentor-email').innerText = data.email;
    document.getElementById('mentor-phone').innerText = data.phone;
    document.getElementById('mentor-place').innerText = data.place;
    document.getElementById('mentor-hobbies').innerText = data.hobbies;
    document.getElementById('mentor-image').src = data.image;
  })
  .catch(error => {
    console.error('Error fetching mentor data:', error);
    alert('Failed to load mentor data');
  });

// Sample student images by year (replace with real image paths)
const studentPhotos = {
  2024: [
    "https://via.placeholder.com/600x400?text=Student+1+2024",
    "https://via.placeholder.com/600x400?text=Student+2+2024"
  ],
  2025: [
    "https://via.placeholder.com/600x400?text=Student+1+2025",
    "https://via.placeholder.com/600x400?text=Student+2+2025"
  ],
  2026: [
    "https://via.placeholder.com/600x400?text=Student+1+2026",
    "https://via.placeholder.com/600x400?text=Student+2+2026"
  ],
  2027: [
    "https://via.placeholder.com/600x400?text=Student+1+2027",
    "https://via.placeholder.com/600x400?text=Student+2+2027"
  ],
  2028: [
    "https://via.placeholder.com/600x400?text=Student+1+2028",
    "https://via.placeholder.com/600x400?text=Student+2+2028"
  ]
};

function loadCarousel(year) {
  const images = studentPhotos[year] || [];
  const carouselInner = document.getElementById("carouselInner");
  carouselInner.innerHTML = "";

  images.forEach((src, index) => {
    const div = document.createElement("div");
    div.className = "carousel-item" + (index === 0 ? " active" : "");
    div.innerHTML = `<img src="${src}" class="d-block w-100" alt="Student Photo">`;
    carouselInner.appendChild(div);
  });
}
