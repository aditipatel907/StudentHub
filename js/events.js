fetch("../json/events.json")
    .then(response => response.json())
    .then(data => {
        console.log(data);
    });

fetch("../JSON/events.json")
    .then(response => response.json())
    .then(data => {

        const container = document.getElementById("eventContainer");

        data.forEach(event => {

            const card = document.createElement("div");

            card.className = "event-card";

            card.innerHTML = `
                <h3>${event.title}</h3>
                <p><b>Date:</b> ${event.date}</p>
                <p><b>Venue:</b> ${event.venue}</p>
                <p><b>Category:</b> ${event.category.join(", ")}</p>
                <p>${event.description}</p>
                <button>Register Now</button>
            `;

            container.appendChild(card);
        });

    });