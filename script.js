document.addEventListener('DOMContentLoaded', () => {
    const searchForm = document.querySelector('#searchForm');
    const specialityDropdown = document.querySelector('#specialityDropdown');

    // Fetch specialities when the page loads
    fetchSpecialities();

    searchForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const speciality = document.getElementById('specialityDropdown').value;

        if (!speciality) {
            alert('Please select a speciality.');
            return;
        }

        console.log(`Redirecting to results.html?speciality=${encodeURIComponent(speciality)}`);
        window.location.href = `results.html?speciality=${encodeURIComponent(speciality)}`;
    });
});

async function fetchSpecialities() {
    try {
        const response = await fetch('http://localhost:3000/search?q=');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        const specialityDropdown = document.querySelector('#specialityDropdown');

        specialityDropdown.innerHTML = '<option value="">Select Speciality</option>';
        const specialities = new Set(data.map(doctor => doctor.speciality));
        specialities.forEach(speciality => {
            const option = document.createElement('option');
            option.value = speciality;
            option.textContent = speciality;
            specialityDropdown.appendChild(option);
        });
    } catch (error) {
        console.error('Error fetching specialities:', error);
    }
}
