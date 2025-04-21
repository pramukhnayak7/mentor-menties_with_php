document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const popup = document.getElementById('messagePopup');
    const openBtn = document.getElementById('openPopup');
    const closeBtn = document.querySelector('.close-btn');
    const messageForm = document.getElementById('messageForm');
    
    // Open popup
    openBtn.addEventListener('click', function() {
        popup.style.display = 'flex';
    });
    
    // Close popup when clicking X
    closeBtn.addEventListener('click', function() {
        popup.style.display = 'none';
    });
    
    // Close popup when clicking outside
    popup.addEventListener('click', function(e) {
        if (e.target === popup) {
            popup.style.display = 'none';
        }
    });
    
    // Handle form submission
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = document.getElementById('messageText').value;
        
        // Here you would typically send the message to a server
        console.log('Message sent:', message);
        alert('Message sent successfully!');
        
        // Clear and close
        document.getElementById('messageText').value = '';
        popup.style.display = 'none';
    });
});