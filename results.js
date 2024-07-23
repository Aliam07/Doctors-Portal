document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const speciality = urlParams.get('speciality');

    fetchDoctors(speciality);
});

async function fetchDoctors(speciality) {
    try {
        console.log(`Fetching doctors with speciality: ${speciality}`);
        const response = await fetch(`http://localhost:3000/search?q=${encodeURIComponent(speciality)}`);
        const results = await response.json();

        if (response.ok) {
            console.log('Doctors fetched successfully:', results);
            displayResults(results);
        } else {
            alert('Error fetching doctors: ' + results.error);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error fetching doctors: ' + error.message);
    }
}

function displayResults(doctors) {
    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = '';

    if (doctors.length === 0) {
        resultsDiv.innerHTML = '<p>No results found.</p>';
    } else {
        const ul = document.createElement('ul');
        ul.classList.add('results-list');

        doctors.forEach(doctor => {
            const li = document.createElement('li');
            li.classList.add('result-item');
            li.innerHTML = `
                <h3>${doctor.doctorname}</h3>
                <p>Speciality: ${doctor.speciality}</p>
            `;
            ul.appendChild(li);
        });

        resultsDiv.appendChild(ul);
    }
}
