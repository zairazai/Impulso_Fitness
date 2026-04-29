document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert');

    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.4s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 400);
            });
        }, 3000);
    }

    const toggleButtons = document.querySelectorAll('.sidebar-toggle-btn');

    toggleButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-target');
            const target = document.getElementById(targetId);

            if (target) {
                target.classList.toggle('open');
            }
        });
    });
});