function showAlert(type, message, duration = 5000) {
    const alertContainer = document.getElementById('alert-container');
    const alert = document.createElement('div');

    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <span style='max-width: 300px;'>${message}</span>
        <span class="alert-close" onclick="this.parentElement.style.display='none'">&times;</span>
    `;

    alertContainer.appendChild(alert);

    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, duration);
}
