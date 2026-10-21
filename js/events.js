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

                <form method="POST" action="../php/event_register.php">
                    <input type="hidden" name="student_id" value="3">
                    <input type="hidden" name="event_id" value="${event.id}">
                    <button type="submit">Register Now</button>
                </form>
            `;

            container.appendChild(card);
        });

    });