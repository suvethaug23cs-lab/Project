// script.js
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('registrationForm');
    const messageDiv = document.getElementById('message');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        // Simple client-side validation
        if (!form.checkValidity()) {
            alert("Please fill all fields correctly.");
            return;
        }

        const formData = new FormData(form);

        // AJAX request
        fetch('process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            messageDiv.innerHTML = `<div class="alert alert-success">${data}</div>`;
            form.style.display = 'none'; // Hide the form after successful registration
        })
        .catch(error => {
            messageDiv.innerHTML = `<div class="alert alert-danger">Error: ${error}</div>`;
        });
    });
});