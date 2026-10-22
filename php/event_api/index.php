<?php
session_start();

if (
    !isset($_SESSION["student_id"]) ||
    strtolower(trim((string)($_SESSION["role"] ?? ""))) !== "admin"
) {
    exit("Access denied. Please log in with an admin account.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>StudentHub - AJAX Event CRUD</title>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1100px;
    margin: 30px auto;
    padding: 15px;
    background: #f4f6fb;
    color: #202938;
}

form, .event-card {
    background: white;
    padding: 18px;
    margin: 15px 0;
    border-radius: 8px;
}

input, textarea, select {
    display: block;
    box-sizing: border-box;
    width: 100%;
    padding: 10px;
    margin: 8px 0 14px;
}

button {
    padding: 9px 14px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin: 4px;
}

.save { background: #2457d6; color: white; }
.edit { background: #e5edff; }
.delete { background: #c62828; color: white; }

#message {
    padding: 10px;
    margin: 12px 0;
}

.event-card h3 { margin-bottom: 5px; }
.event-card p { overflow-wrap: anywhere; }
</style>
</head>

<body>

<h1>Event Management - Fetch API</h1>
<p>Add, view, edit, and delete events without reloading the page.</p>

<div id="message" role="status"></div>

<h2 id="formHeading">Add Event</h2>

<form id="eventForm">
    <input type="hidden" id="event_id">

    <label>Event Title</label>
    <input type="text" id="title" required maxlength="150">

    <label>Description</label>
    <textarea id="description" required></textarea>

    <label>Event Date</label>
    <input type="date" id="event_date" required>

    <label>Event Time</label>
    <input type="time" id="event_time" required>

    <label>Venue</label>
    <input type="text" id="venue" required maxlength="150">

    <label>Category</label>
    <select id="category" required>
        <option value="">Select Category</option>
        <option>Technical</option>
        <option>Non-Technical</option>
        <option>Seminars</option>
        <option>Workshops</option>
        <option>Competitions</option>
        <option>Sports</option>
        <option>Cultural</option>
        <option>Hackathon</option>
    </select>

    <button class="save" type="submit">Save Event</button>
    <button type="button" onclick="resetForm()">Clear</button>
</form>

<h2>All Events</h2>
<div id="eventList">Loading events...</div>

<script>
const API = "api.php";

const message = document.getElementById("message");
const eventList = document.getElementById("eventList");
const eventForm = document.getElementById("eventForm");

function showMessage(text, success = true) {
    message.textContent = text;
    message.style.background = success ? "#dff5e5" : "#ffdddd";
}

async function requestAPI(method, data = null) {
    const options = { method };

    if (data !== null) {
        options.headers = {
            "Content-Type": "application/json"
        };
        options.body = JSON.stringify(data);
    }

    const response = await fetch(API, options);
    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || "Request failed.");
    }

    return result;
}

// LIST EVENTS
async function loadEvents() {
    try {
        const result = await requestAPI("GET");
        displayEvents(result.data || []);
    } catch (error) {
        eventList.textContent = error.message;
        showMessage(error.message, false);
    }
}

// Display events safely without inserting user text as HTML.
function displayEvents(events) {
    eventList.replaceChildren();

    if (events.length === 0) {
        eventList.textContent = "No events found.";
        return;
    }

    events.forEach(event => {
        const card = document.createElement("div");
        card.className = "event-card";

        const title = document.createElement("h3");
        title.textContent = event.title;

        const description = document.createElement("p");
        description.textContent = event.description;

        const details = document.createElement("p");
        details.textContent =
            `${event.event_date} | ${event.event_time} | ` +
            `${event.venue} | ${event.category}`;

        const editButton = document.createElement("button");
        editButton.className = "edit";
        editButton.textContent = "Edit";
        editButton.addEventListener("click", () => editEvent(event));

        const deleteButton = document.createElement("button");
        deleteButton.className = "delete";
        deleteButton.textContent = "Delete";
        deleteButton.addEventListener("click", () => deleteEvent(event.event_id));

        card.append(title, description, details, editButton, deleteButton);
        eventList.appendChild(card);
    });
}

// ADD OR UPDATE EVENT
eventForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    const id = document.getElementById("event_id").value;

    const data = {
        title: document.getElementById("title").value.trim(),
        description: document.getElementById("description").value.trim(),
        event_date: document.getElementById("event_date").value,
        event_time: document.getElementById("event_time").value,
        venue: document.getElementById("venue").value.trim(),
        category: document.getElementById("category").value
    };

    try {
        let result;

        if (id) {
            data.event_id = Number(id);
            result = await requestAPI("PUT", data);
        } else {
            result = await requestAPI("POST", data);
        }

        showMessage(result.message);
        resetForm();
        await loadEvents();

    } catch (error) {
        showMessage(error.message, false);
    }
});

// LOAD SELECTED EVENT INTO FORM
function editEvent(event) {
    document.getElementById("event_id").value = event.event_id;
    document.getElementById("title").value = event.title;
    document.getElementById("description").value = event.description;
    document.getElementById("event_date").value = event.event_date;
    document.getElementById("event_time").value =
        String(event.event_time).substring(0, 5);
    document.getElementById("venue").value = event.venue;
    document.getElementById("category").value = event.category;

    document.getElementById("formHeading").textContent = "Edit Event";
    window.scrollTo({ top: 0, behavior: "smooth" });
}

// DELETE EVENT
async function deleteEvent(id) {
    if (!confirm("Are you sure you want to delete this event?")) {
        return;
    }

    try {
        const result = await requestAPI("DELETE", {
            event_id: Number(id)
        });

        showMessage(result.message);
        await loadEvents();

    } catch (error) {
        showMessage(error.message, false);
    }
}

// RESET FORM
function resetForm() {
    eventForm.reset();
    document.getElementById("event_id").value = "";
    document.getElementById("formHeading").textContent = "Add Event";
}

// Load list when page opens.
loadEvents();
</script>

</body>
</html>