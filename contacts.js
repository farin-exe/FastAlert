// DOM Elements
const contactsList = document.getElementById('contactsList');
const addContactForm = document.getElementById('addContactForm');
const contactCount = document.getElementById('contactCount');

// Load contacts from LocalStorage or use Dummy Data
let guardians = JSON.parse(localStorage.getItem('guardians')) || [
    { id: 1, name: "Mother", phone: "01700000000", relation: "Family" },
    { id: 2, name: "Police Control", phone: "999", relation: "Official" }
];

// Function to Render Contacts
function renderContacts() {
    contactsList.innerHTML = ""; // Clear current list

    guardians.forEach(guardian => {
        const card = document.createElement('div');
        card.className = "col-12";

        // Determine Icon based on relation
        let icon = "fa-user";
        if (guardian.relation === "Official") icon = "fa-building-shield";
        if (guardian.relation === "Family") icon = "fa-house-user";

        card.innerHTML = `
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-purple-light text-purple rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background-color: #e2d9f3;">
                            <i class="fas ${icon} fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">${guardian.name}</h6>
                            <small class="text-muted">${guardian.phone}</small>
                            <span class="badge bg-light text-secondary border ms-2">${guardian.relation}</span>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="deleteContact(${guardian.id})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        contactsList.appendChild(card);
    });

    // Update Counter
    contactCount.innerText = `${guardians.length}/5`;
}

// Function to Add Contact
addContactForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const name = document.getElementById('contactName').value;
    const phone = document.getElementById('contactPhone').value;
    const relation = document.getElementById('contactRelation').value;

    const newGuardian = {
        id: Date.now(), // Unique ID based on timestamp
        name: name,
        phone: phone,
        relation: relation
    };

    guardians.push(newGuardian);
    saveAndRender();

    // Close Modal manually (Bootstrap API)
    const modalEl = document.getElementById('addContactModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();

    // Reset Form
    addContactForm.reset();
});

// Function to Delete Contact
window.deleteContact = (id) => {
    if (confirm("Remove this guardian?")) {
        guardians = guardians.filter(g => g.id !== id);
        saveAndRender();
    }
};

// Save to LocalStorage and Update UI
function saveAndRender() {
    localStorage.setItem('guardians', JSON.stringify(guardians));
    renderContacts();
}

// Initial Render
renderContacts();